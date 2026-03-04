<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Application\Service\CsrfService;
use Laminas\View\Helper\AbstractHelper;

final class CsrfToken extends AbstractHelper
{
    public function __construct(
        private readonly CsrfService $csrf,
    ) {}

    public function __invoke(string $formId): string
    {
        return $this->csrf->generate($formId);
    }
}