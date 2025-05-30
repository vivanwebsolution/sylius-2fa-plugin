<?php

declare(strict_types=1);

namespace VivanWebSolution\Sylius2FAPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class VivanWebSolutionSylius2FAPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
