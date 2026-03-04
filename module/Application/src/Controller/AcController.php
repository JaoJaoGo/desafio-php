<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Service\AcService;
use Application\Service\QrCodeService;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

final class AcController extends AbstractActionController
{
    public function __construct(
        private readonly AcService $acs,
        private readonly QrCodeService $qr,
    ) {}

    public function indexAction(): ViewModel
    {
        return new ViewModel([
            'acs' => $this->acs->list(),
        ]);
    }

    public function newAction(): ViewModel|Response
    {
        $request = $this->getRequest();

        $error = null;
        $name = '';

        if ($request->isPost()) {
            $name = (string) $this->params()->fromPost('name', '');

            try {
                $this->acs->create($name);
                return $this->redirect()->toRoute('acs');
            } catch (\InvalidArgumentException $e) {
                $error = $e->getMessage();
            }
        }

        return new ViewModel([
            'error' => $error,
            'name'  => $name,
        ]);
    }

    public function editAction(): ViewModel|Response
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $ac = $this->acs->find($id);
        if (!$ac) {
            return $this->notFoundAction();
        }

        $request = $this->getRequest();
        $error = null;

        if ($request->isPost()) {
            $name = (string) $this->params()->fromPost('name', '');

            try {
                $this->acs->update($ac, $name);
                return $this->redirect()->toRoute('acs');
            } catch (\InvalidArgumentException $e) {
                $error = $e->getMessage();
            }
        }

        return new ViewModel([
            'ac'    => $ac,
            'error' => $error,
        ]);
    }

    public function deleteAction(): Response
    {
        $request = $this->getRequest();

        // delete via POST (CSRF global valida)
        if (!$request->isPost()) {
            $response = $this->getResponse();
            $response->setStatusCode(405);
            return $response;
        }

        $id = (int) $this->params()->fromRoute('id', 0);

        $ac = $this->acs->find($id);
        if (!$ac) {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return $response;
        }

        $this->acs->delete($ac);

        return $this->redirect()->toRoute('acs');
    }

    public function qrcodeAction(): Response
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $ac = $this->acs->find($id);
        if (!$ac) {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return $response;
        }

        $url = sprintf('http://localhost:8080/acs/%d', $ac->getId());
        $png = $this->qr->renderPng($url, 6);

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'image/png');
        $response->setContent($png);

        return $response;
    }
}