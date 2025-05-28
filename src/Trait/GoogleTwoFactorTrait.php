<?php

declare(strict_types=1);

namespace VivanWebSolution\Sylius2FAPlugin\Trait;

use Doctrine\ORM\Mapping as ORM;

trait GoogleTwoFactorTrait
{
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $googleAuthenticatorSecret = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isGoogleAuthenticatorEnabled = false;

    public function isGoogleAuthenticatorEnabled(): bool
    {
        return $this->isGoogleAuthenticatorEnabled;
    }

    public function setIsGoogleAuthenticatorEnabled(bool $enabled): self
    {
        $this->isGoogleAuthenticatorEnabled = $enabled;
        return $this;
    }

    public function getGoogleAuthenticatorSecret(): ?string
    {
        return $this->googleAuthenticatorSecret;
    }

    public function setGoogleAuthenticatorSecret(?string $secret): self
    {
        $this->googleAuthenticatorSecret = $secret;
        return $this;
    }

    public function getGoogleAuthenticatorUsername(): string
    {
        return method_exists($this, 'getUsername') ? $this->getUsername() : 'admin';
    }
}
    