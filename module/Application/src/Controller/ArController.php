<?php declare(strict_types=1);

namespace Application\Controller;

use Application\Entity\Ar;
use Application\Service\ArService;
use Application\Service\QrCodeService;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

final class ArController extends AbstractActionController
{
    public function __construct(
        private readonly ArService $ars,
        private readonly QrCodeService $qr,
        private readonly string $baseUrl,
    ) {}

    public function indexAction(): ViewModel
    {
        return new ViewModel([
            'items' => $this->ars->list(),
        ]);
    }

    public function newAction(): ViewModel|Response
    {
        $request = $this->getRequest();

        $error = null;
        $name = '';
        $acN2Id = 0;

        $acN2s = $this->ars->listAcN2s();
        if (count($acN2s) === 0) {
            return new ViewModel([
                'error' => 'Você precisa criar uma AC N2 antes de cadastrar um AR.',
                'name' => $name,
                'acN2Id' => $acN2Id,
                'acN2s' => $acN2s,
            ]);
        }

        if ($request->isPost()) {
            $name = (string) $this->params()->fromPost('name', '');
            $acN2Id = (int) $this->params()->fromPost('ac_n2_id', 0);

            $acN2 = $this->ars->findAcN2($acN2Id);

            if (!$acN2) {
                $error = 'AC N2 inválida.';
            } else {
                try {
                    $this->ars->create($acN2, $name);
                    return $this->redirect()->toRoute('ars');
                } catch (\InvalidArgumentException $e) {
                    $error = $e->getMessage();
                }
            }
        }

        return new ViewModel([
            'error' => $error,
            'name' => $name,
            'acN2Id' => $acN2Id,
            'acN2s' => $acN2s,
        ]);
    }

    public function editAction(): ViewModel|Response
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $item = $this->ars->find($id);
        if (!$item) {
            return $this->notFoundAction();
        }

        $request = $this->getRequest();

        $error = null;
        $name = $item->getName();
        $acN2Id = $item->getAcN2()->getId() ?? 0;

        $acN2s = $this->ars->listAcN2s();

        if ($request->isPost()) {
            $name = (string) $this->params()->fromPost('name', '');
            $acN2Id = (int) $this->params()->fromPost('ac_n2_id', 0);

            $acN2 = $this->ars->findAcN2($acN2Id);
            if (!$acN2) {
                $error = 'AC N2 inválida.';
            } else {
                try {
                    $this->ars->update($item, $acN2, $name);
                    return $this->redirect()->toRoute('ars');
                } catch (\InvalidArgumentException $e) {
                    $error = $e->getMessage();
                }
            }
        }

        return new ViewModel([
            'item' => $item,
            'error' => $error,
            'name' => $name,
            'acN2Id' => $acN2Id,
            'acN2s' => $acN2s,
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

        $item = $this->ars->find($id);
        if (!$item) {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return $response;
        }

        $this->ars->delete($item);

        return $this->redirect()->toRoute('ars');
    }

    public function viewAction(): ViewModel|Response
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $item = $this->ars->find($id);
        if (!$item) {
            return $this->notFoundAction();
        }

        $acn2 = $item->getAcN2();
        $ac = $acn2->getAc();

        return new ViewModel([
            'item' => $item,
            'acn2' => $acn2,
            'ac' => $ac,
        ]);
    }

    public function qrcodeAction(): Response
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        /** @var Ar|null $item */
        $item = $this->ars->find($id);
        if (!$item) {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return $response;
        }

        $url = sprintf('%s/ars/%d', $this->baseUrl, $item->getId());
        $png = $this->qr->renderPng($url, 6);

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'image/png');
        $response->setContent($png);

        return $response;
    }
}
