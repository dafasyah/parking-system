<?php
namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QRCodeService
{
    public function generateAndStore(string $code): string
    {
        $svg = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($code);
        
        // Save to public disk (storage/app/public/qrcodes/)
        $filename = $code . '.svg';
        $path = 'qrcodes/' . $filename;
        
        Storage::disk('public')->put($path, $svg);
        
        return $path;
    }
}
