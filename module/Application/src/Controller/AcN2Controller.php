<?php

namespace Application\Controller;

use Application\Entity\AcN2;
use Application\Service\AcN2Service;
use Application\Service\QrCodeService;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

final class AcN2Controller extends AbstractActionController
{
    public function __construct(
        private readonly AcN2Service $acn2s,
        private readonly QrCodeService $qr,
        private readonly string $baseUrl,
    ) {}

    public function indexAction(): ViewModel
    {
        return new ViewModel([
            'items' => $this->acn2s->list(),
        ]);
    }

    public function newAction(): ViewModel|Response
    {
        $request = $this->getRequest();

        $error = null;
        $name = '';
        $acId = 0;

        $acs = $this->acn2s->listAcs();
        if (count($acs) === 0) {
            return new ViewModel([
                'error' => 'Você precisa criar uma Ac antes de cadastrar uma ACN2.',
                'name' => $name,
                'acId' => $acId,
                'acs' => $acs,
            ]);
        }

        if ($request->isPost()) {
            $name = (string) $this->params()->fromPost('name', '');
            $acId = (int) $this->params()->fromPost('ac_id', 0);

            $ac = $this->acn2s->findAc($acId);

            if (!$ac) {
                $error = 'AC inválida';
            } else {
                try {
                    $this->acn2s->create($ac, $name);

                    return $this->redirect()->toRoute('ac-n2');
                } catch (\InvalidArgumentException $e) {
                    $error = $e->getMessage();
                }
            }
        }

        return new ViewModel([
            'error' => $error,
            'name' => $name,
            'acId' => $acId,
            'acs' => $acs,
        ]);
    }

    public function editAction(): ViewModel|Response
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $item = $this->acn2s->find($id);
        if (!$item) {
            return $this->notFoundAction();
        }

        $request = $this->getRequest();

        $error = null;
        $name = $item->getName();
        $acId = $item->getAc()->getId() ?? 0;

        $acs = $this->acn2s->listAcs();

        if ($request->isPost()) {
            $name = (string) $this->params()->fromPost('name', '');
            $acId = (int) $this->params()->fromPost('ac_id', 0);

            $ac = $this->acn2s->findAc($acId);
            if (!$ac) {
                $error = 'AC inválida.';
            } else {
                try {
                    $this->acn2s->update($item, $ac, $name);

                    return $this->redirect()->toRoute('ac-n2');
                } catch (\InvalidArgumentException $e) {
                    $error = $e->getMessage();
                }
            }
        }

        return new ViewModel([
            'item' => $item,
            'error' => $error,
            'name' => $name,
            'acId' => $acId,
            'acs' => $acs,
        ]);
    }

    public function deleteAction(): Response
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            $response = $this->getResponse();
            $response->setStatusCode(405);

            return $response;
        }

        $id = (int) $this->params()->fromRoute('id', 0);

        $item = $this->acn2s->find($id);
        if (!$item) {
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return $response;
        }

        $this->acn2s->delete($item);

        return $this->redirect()->toRoute('ac-n2');
    }

    public function viewAction(): ViewModel|Response
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $item = $this->acn2s->find($id);
        if (!$item) {
            return $this->notFoundAction();
        }

        $parent = $item->getAc();
        $children = $this->acn2s->listChildren($item);

        return new ViewModel([
            'item' => $item,
            'ac' => $parent,
            'ars' => $children,
        ]);
    }

    public function qrcodeAction(): Response
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        /** @var AcN2|null $item */
        $item = $this->acn2s->find($id);
        if (!$item) {
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return $response;
        }

        $url = sprintf('%s/ac-n2/%d', $this->baseUrl, $item->getId());
        $png = $this->qr->renderPng($url, 6);

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'image/png');
        $response->setContent($png);

        return $response;
    }
}
