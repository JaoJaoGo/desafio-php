<?php declare(strict_types=1);

namespace Application;

use Application\Command\CreateUserCommand;
use Application\Service\AuthService;
use Application\Service\CsrfService;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\InitProviderInterface;
use Laminas\ModuleManager\ModuleManagerInterface;
use Laminas\Mvc\MvcEvent;
use Symfony\Component\Console\Application as ConsoleApplication;

final class Module implements ConfigProviderInterface, InitProviderInterface, BootstrapListenerInterface
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * DoctrineModule CLI integration point.
     * Adds custom Symfony Console commands to doctrine-module CLI.
     */
    public function init(ModuleManagerInterface $manager): void
    {
        $sharedEvents = $manager->getEventManager()->getSharedManager();

        $sharedEvents->attach('doctrine', 'loadCli.post', function (EventInterface $e): void {
            $cli = $e->getTarget();

            if (!$cli instanceof ConsoleApplication) {
                return;
            }

            $container = $e->getParam('ServiceManager');
            $cli->add($container->get(CreateUserCommand::class));
        });
    }

    public function onBootstrap(EventInterface $e): void
    {
        /** @var MvcEvent $e */
        $app = $e->getTarget();
        $events = $app->getEventManager();

        // 1) Auth guard (rotas protegidas) - se você já tem, pode ignorar esta parte
        $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'enforceAuth'], -100);

        // 2) CSRF guard global (opt-in por rota)
        $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'enforceCsrf'], -90);
    }

    public function enforceAuth(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        /** @var AuthService $auth */
        $auth = $sm->get(AuthService::class);

        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            return null;
        }

        $routeName = (string) $routeMatch->getMatchedRouteName();

        $config = $sm->get('config');
        $publicRoutes = $config['application']['auth']['public_routes'] ?? [];

        if (in_array($routeName, $publicRoutes, true)) {
            return null;
        }

        if ($auth->check()) {
            return null;
        }

        // redirect para login mantendo destino
        $request = $e->getRequest();
        $uri = method_exists($request, 'getUri') ? (string) $request->getUri() : '';
        $loginUrl = '/login' . ($uri !== '' ? '?redirect=' . urlencode($uri) : '');

        $response = $e->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $loginUrl);
        $response->setStatusCode(302);

        $e->stopPropagation(true);
        return $response;
    }

    public function enforceCsrf(MvcEvent $e)
    {
        $request = $e->getRequest();

        $method = strtoupper((string) $request->getMethod());
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return null;
        }

        $sm = $e->getApplication()->getServiceManager();
        $config = $sm->get('config');

        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            return null;
        }

        $routeName = (string) $routeMatch->getMatchedRouteName();

        $csrfRoutes = $config['csrf']['routes'] ?? [];
        if (!is_array($csrfRoutes) || !array_key_exists($routeName, $csrfRoutes)) {
            return null;
        }

        $formPattern = (string) $csrfRoutes[$routeName];
        $formId = $this->resolveFormId($formPattern, $routeMatch->getParams());

        /** @var \Application\Service\CsrfService $csrf */
        $csrf = $sm->get(\Application\Service\CsrfService::class);

        $post = method_exists($request, 'getPost') ? $request->getPost() : null;
        $token = '';

        if ($post && isset($post['csrf'])) {
            $token = (string) $post['csrf'];
        }

        if (!$csrf->isValid($formId, $token)) {
            $response = $e->getResponse();

            $referer = $request->getHeader('Referer');
            $fallbackUrl = '/';

            if ($routeName === 'logout') {
                $fallbackUrl = '/login';
            }

            $redirectUrl = $fallbackUrl;

            if ($referer && method_exists($referer, 'getUri')) {
                $redirectUrl = (string) $referer->getUri();
            }

            $response->getHeaders()->addHeaderLine('Location', $redirectUrl);
            $response->setStatusCode(303);

            $e->stopPropagation(true);
            return $response;
        }

        return null;
    }

    /**
     * @param array<string,mixed> $params
     */
    private function resolveFormId(string $pattern, array $params): string
    {
        return preg_replace_callback('/\{(\w+)\}/', function (array $m) use ($params): string {
            $key = $m[1];
            return array_key_exists($key, $params) ? (string) $params[$key] : '';
        }, $pattern) ?: $pattern;
    }
}
