<?php
declare(strict_types=1);

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QRCodeService
{
    public function generatePng(string $data): string
    {
        $qrCode = QrCode::create($data);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        return $result->getString();
    }

    public function generateBase64(string $data): string
    {
        return 'data:image/png;base64,' . base64_encode($this->generatePng($data));
    }

    public function generateForHouse(int $houseId, string $houseName): string
    {
        $url = BASE_URL . "/batches?house_id=$houseId";
        return $this->generateBase64("HOUSE:$houseId|$houseName|$url");
    }
}
