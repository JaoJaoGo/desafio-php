<?php declare(strict_types=1);

namespace Application;

use Application\Command\Factory\CreateUserCommandFactory;
use Application\Command\CreateUserCommand;
use Application\Controller\Factory\AcControllerFactory;
use Application\Controller\Factory\AuthControllerFactory;
use Application\Controller\AcController;
use Application\Controller\AuthController;
use Application\Service\Factory\AcServiceFactory;
use Application\Service\Factory\AuthServiceFactory;
use Application\Service\Factory\CsrfServiceFactory;
use Application\Service\Factory\QrCodeServiceFactory;
use Application\Service\AcService;
use Application\Service\AuthService;
use Application\Service\CsrfService;
use Application\Service\QrCodeService;
use Application\View\Helper\Factory\CsrfTokenFactory;
use Application\View\Helper\CsrfToken;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    /** Definimos aqui quais as rotas públicas (não precisam de login) */
    'application' => [
        'auth' => [
            'public_routes' => [
                'login',
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'login' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/login',
                    'defaults' => [
                        'controller' => AuthController::class,
                        'action' => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/logout',
                    'defaults' => [
                        'controller' => AuthController::class,
                        'action' => 'logout',
                    ],
                ],
            ],
            'acs' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/acs',
                    'defaults' => [
                        'controller' => AcController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'new' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/new',
                            'defaults' => [
                                'action' => 'new',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/:id/edit',
                            'defaults' => [
                                'action' => 'edit',
                            ],
                        ],
                    ],
                    'delete' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/:id/delete',
                            'defaults' => [
                                'action' => 'delete',
                            ],
                        ],
                    ],
                    'qrcode' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/:id/qrcode',
                            'defaults' => [
                                'action' => 'qrcode',
                            ],
                        ],
                    ],
                ],
            ],
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'application' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            AuthController::class => AuthControllerFactory::class,
            AcController::class => AcControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'layout' => 'layout/app',
        'template_map' => [
            'layout/app' => __DIR__ . '/../view/layout/app.phtml',
            'layout/auth' => __DIR__ . '/../view/layout/auth.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'application/ac/index' => __DIR__ . '/../view/application/ac/index.phtml',
            'application/ac/new' => __DIR__ . '/../view/application/ac/new.phtml',
            'application/ac/edit' => __DIR__ . '/../view/application/ac/edit.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'service_manager' => [
        'factories' => [
            CreateUserCommand::class => CreateUserCommandFactory::class,
            AuthService::class => AuthServiceFactory::class,
            CsrfService::class => CsrfServiceFactory::class,
            AcService::class => AcServiceFactory::class,
            QrCodeService::class => QrCodeServiceFactory::class,
        ],
    ],
    'view_helpers' => [
        'factories' => [
            CsrfToken::class => CsrfTokenFactory::class,
        ],
        'aliases' => [
            'csrfToken' => CsrfToken::class,
        ],
    ],
    'csrf' => [
        'routes' => [
            'login' => 'login_form',
            'logout' => 'logout_form',
            'acs/new' => 'ac_new',
            'acs/edit' => 'ac_edit_{id}',
            'acs/delete' => 'ac_delete_{id}',
        ],
    ],
];
