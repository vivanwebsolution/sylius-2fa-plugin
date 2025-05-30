<?php

declare(strict_types=1);

namespace Tests\VivanWebSolution\Sylius2FAPlugin\Behat\Page\Shop;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Webmozart\Assert\Assert;

class DynamicWelcomePage extends SymfonyPage implements WelcomePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getGreeting(): string
    {
        $greeting = $this->getSession()->getPage()->waitFor(3, function (): string {
            $greeting = $this->getElement('greeting')->getText();

            if ('Loading...' === $greeting) {
                return '';
            }

            return $greeting;
        });

        Assert::string($greeting, 'Greeting should be a string, but it is not.');

        return $greeting;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'acme_sylius_example_dynamic_welcome';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'greeting' => '[data-test-dynamic-greeting]',
        ]);
    }
}
