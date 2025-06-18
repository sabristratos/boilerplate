<?php

namespace App\Services;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthenticationService
{
    /**
     * The Google2FA instance.
     *
     * @var \PragmaRX\Google2FA\Google2FA
     */
    protected $engine;

    /**
     * Create a new two factor authentication service instance.
     *
     * @return void
     */
    public function __construct(Google2FA $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Generate a new secret key.
     */
    public function generateSecretKey(): string
    {
        return $this->engine->generateSecretKey();
    }

    /**
     * Generate a new recovery code array.
     */
    public function generateRecoveryCodes(): array
    {
        $recoveryCodes = [];

        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = $this->generateRecoveryCode();
        }

        return $recoveryCodes;
    }

    /**
     * Generate a new recovery code.
     */
    protected function generateRecoveryCode(): string
    {
        return bin2hex(random_bytes(10));
    }

    /**
     * Get the QR code SVG for the given secret key.
     */
    public function qrCodeSvg(string $companyName, string $companyEmail, string $secret): string
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                new SvgImageBackEnd()
            )
        ))->writeString($this->engine->getQRCodeUrl($companyName, $companyEmail, $secret));

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }

    /**
     * Verify the given code.
     */
    public function verify(string $secret, string $code): bool
    {
        return $this->engine->verifyKey($secret, $code);
    }
}
