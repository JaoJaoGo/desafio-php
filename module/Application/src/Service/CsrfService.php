<?php

declare(strict_types=1);

namespace Application\Service;

use Laminas\Validator\Csrf as CsrfValidator;

final class CsrfService
{
    public function generate(string $formId): string
    {
        $v = new CsrfValidator(['name' => $formId]);

        // Força o token a existir na session
        $v->getHash();

        return $v->getHash();
    }

    public function isValid(string $formId, ?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        $v = new CsrfValidator(['name' => $formId]);

        return $v->isValid($token);
    }
}