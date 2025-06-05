# Sylius 2FA Google Authenticator Plugin

This plugin enables **Google Two-Factor Authentication (2FA)** for Sylius 2.0 Admin Users using:

- [`scheb/2fa-bundle`](https://github.com/scheb/2fa-bundle)
- [`scheb/2fa-google-authenticator`](https://github.com/scheb/2fa-google-authenticator)

It allows admins to secure their accounts with Google Authenticator time-based one-time passwords (TOTP).

---

## Features

- Seamless integration of Google Authenticator for 2FA on Sylius Admin Users
- Admin UI toggle to enable/disable 2FA per user
- AJAX-enabled toggle with CSRF protection
- Extends the Sylius `AdminUser` entity to support `TwoFactorInterface`
- QR code generation for quick mobile setup using [`endroid/qr-code`](https://github.com/endroid/qr-code)

---

## Requirements

- Sylius 2.0
- PHP 8.x
- Composer

---

## Installation

### 1. Install via Composer

```bash
composer require vivanwebsolution/sylius-2fa-plugin
```
### 2. Enable the Bundle
In config/bundles.php, register the bundle:
 ```bash
 return [
    // ...
    Scheb\TwoFactorBundle\SchebTwoFactorBundle::class => ['all' => true],
];
```
### 3. Configure the Bundle
Create the config file at config/packages/scheb_2fa.yaml:
 ```bash
 scheb_two_factor:
    security_tokens:
        - Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken
        - Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken
    google:
        enabled: true
        server_name: 'Sylius Admin'
```
To clear the Symfony cache, run:
```bash
php bin/console cache:clear
```
### 4. Extend the AdminUser Entity
Modify your AdminUser entity to implement the 2FA interface:
```bash
<?php

declare(strict_types=1);

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\AdminUser as BaseAdminUser;
use VivanWebSolution\Sylius2FAPlugin\Trait\GoogleTwoFactorTrait;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_admin_user')]
class AdminUser extends BaseAdminUser implements TwoFactorInterface
{
    use GoogleTwoFactorTrait;
}
```

### 5. Run Database Migrations
Ensure your database is configured, then run:
```bash
php bin/console doctrine:schema:update --force
```
### 6. Override the Admin User Form Template
Create or override the following template:
```bash
templates/bundles/SyliusAdminBundle/admin_user/form/sections.html.twig
```
Include the 2FA section:
```bash
{% include '@VivanWebSolutionSylius2FAPlugin/admin/sections.html.twig' %}
```
### 7. Configure Routes
Add the plugin routes to config/routes.yaml:
```bash
vivan_sylius_2fa_plugin_admin:
    resource: '@VivanWebSolutionSylius2FAPlugin/config/admin_routing.yaml'
    prefix: /admin
```

## Usage

1. **Log in** to the Sylius Admin panel.

2. Navigate to the **Admin User detail page**.

3. Use the **checkbox** in the "Two-Factor Authentication" section to enable or disable Google 2FA for that admin user.

4. When 2FA is enabled, the user will be prompted to enter a **Google Authenticator code** after logging in with their credentials.

## References

- [Scheb Two Factor Bundle](https://github.com/scheb/two-factor-bundle) – Provides two-factor authentication support for Symfony.
- [Scheb Google Authenticator](https://github.com/scheb/two-factor-bundle#google-authenticator) – Adds Google Authenticator support to the Scheb bundle.
- [Endroid QR Code](https://github.com/endroid/qr-code) – Used for generating QR codes for Google Authenticator setup.

## Functionality Screenshot

![Functionality Screenshot](docs/functionality/enable.png)
![Functionality Screenshot](docs/functionality/app.png)
![Functionality Screenshot](docs/functionality/qr.png)
![Functionality Screenshot](docs/functionality/verify.png)
![Functionality Screenshot](docs/functionality/verifycode.png)

© Vivan Web Solution — Open-source Sylius plugin for 2FA with Google Authenticator
