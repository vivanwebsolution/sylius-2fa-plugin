<?php

declare(strict_types=1);

namespace VivanWebSolution\Sylius2FAPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('Vivan_Web_solution_Sylius_2FA_Plugin');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
