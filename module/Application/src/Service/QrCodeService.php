<?php

declare(strict_types=1);

namespace Application\Service;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

final class QrCodeService
{
    public function renderPng(string $data, int $scale = 6): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'scale'      => $scale,
        ]);

        return (new QRCode($options))->render($data);
    }
}