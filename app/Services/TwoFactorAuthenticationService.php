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
     * @param  \PragmaRX\Google2FA\Google2FA  $engine
     * @return void
     */
    public function __construct(Google2FA $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Generate a new secret key.
     *
     * @return string
     */
    public function generateSecretKey(): string
    {
        return $this->engine->generateSecretKey();
    }

    /**
     * Generate a new recovery code array.
     *
     * @return array
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
     *
     * @return string
     */
    protected function generateRecoveryCode(): string
    {
        return bin2hex(random_bytes(10));
    }

    /**
     * Get the QR code SVG for the given secret key.
     *
     * @param  string  $companyName
     * @param  string  $companyEmail
     * @param  string  $secret
     * @return string
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
     *
     * @param  string  $secret
     * @param  string  $code
     * @return bool
     */
    public function verify(string $secret, string $code): bool
    {
        return $this->engine->verifyKey($secret, $code);
    }
}
