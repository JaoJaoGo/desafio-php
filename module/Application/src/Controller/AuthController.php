<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Service\AuthService;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

final class AuthController extends AbstractActionController
{
    public function __construct(
        private readonly AuthService $auth,
    ) {}

    public function loginAction(): ViewModel
    {
        $this->layout('layout/auth');

        if ($this->auth->check()) {
            return $this->redirect()->toRoute('home');
        }

        $request = $this->getRequest();
        $error = null;

        if ($request->isPost()) {
            $email = (string) $this->params()->fromPost('email', '');
            $password = (string) $this->params()->fromPost('password', '');

            if ($this->auth->attempt($email, $password)) {
                $redirectTo = (string) $this->params()->fromQuery('redirect', '');
                if ($redirectTo !== '') {
                    return $this->redirect()->toUrl($redirectTo);
                }

                return $this->redirect()->toRoute('home');
            }

            $error = 'Credenciais inválidas.';
        }

        return new ViewModel([
            'error' => $error,
        ]);
    }

    public function logoutAction(): Response
    {
        $request = $this->getRequest();

        // logout via POST (CSRF global valida)
        if (!$request->isPost()) {
            $response = $this->getResponse();
            $response->setStatusCode(405);
            return $response;
        }

        $this->auth->logout();

        return $this->redirect()->toRoute('login');
    }
}