<?php

declare(strict_types=1);

namespace Tests\Application\Entity\User;

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
