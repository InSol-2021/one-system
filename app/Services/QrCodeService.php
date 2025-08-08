<?php

namespace App\Services;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class QrCodeService
{
    private string $qrDirectory;

    public function __construct()
    {
        $this->qrDirectory = public_path('assets/qr-codes/');
        $this->ensureDirectoryExists();
    }

    public function generate2FAQrCode(string $qrCodeUrl, int $userId): string
    {
        try {
            if (!class_exists(ImageRenderer::class)) {
                throw new \Exception('BaconQrCode ImageRenderer class not found');
            }

            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCodeSvg = $writer->writeString($qrCodeUrl);

            $filename = 'qr_2fa_' . $userId . '_' . time() . '.svg';
            $filePath = $this->qrDirectory . $filename;

            file_put_contents($filePath, $qrCodeSvg);

            $this->cleanupOldQrCodes($userId);

            return asset('assets/qr-codes/' . $filename);

        } catch (\Exception $e) {
            return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrCodeUrl);
        }
    }

    private function ensureDirectoryExists(): void
    {
        if (!File::exists($this->qrDirectory)) {
            File::makeDirectory($this->qrDirectory, 0755, true);
        }
    }

    private function cleanupOldQrCodes(int $userId): void
    {
        try {
            $pattern = $this->qrDirectory . 'qr_2fa_' . $userId . '_*.svg';
            $oldFiles = glob($pattern);

            if (count($oldFiles) > 3) {
                usort($oldFiles, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });

                $filesToDelete = array_slice($oldFiles, 3);
                foreach ($filesToDelete as $file) {
                    unlink($file);
                }
            }
        } catch (\Exception $e) {
            Log::warning('QR Code cleanup failed: ' . $e->getMessage());
        }
    }

    public function cleanupAllQrCodes(): int
    {
        try {
            $pattern = $this->qrDirectory . 'qr_2fa_*.svg';
            $files = glob($pattern);
            $deleted = 0;

            foreach ($files as $file) {
                if (filemtime($file) < (time() - 86400)) {
                    unlink($file);
                    $deleted++;
                }
            }

            return $deleted;
        } catch (\Exception $e) {
            Log::warning('Bulk QR Code cleanup failed: ' . $e->getMessage());
            return 0;
        }
    }
}
