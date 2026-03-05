<?php

declare(strict_types=1);

namespace Application\Service;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

final class QrCodeService
{
    public function renderPng(string $text, int $scale = 6): string
    {
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('Extensão GD não está carregada (ext-gd).');
        }

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale'      => max(1, $scale),

            // garantir que o retorno seja "raw binary"
            'imageBase64' => false,
        ]);

        return (new QRCode($options))->render($text);
    }
}