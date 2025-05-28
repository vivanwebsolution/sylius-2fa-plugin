<?php

namespace VivanWebSolution\Sylius2FAPlugin\EventSubscriber;

use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AdminLoginSubscriber implements EventSubscriberInterface
{
    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            InteractiveLoginEvent::class => 'onAdminLogin',
        ];
    }

    public function onAdminLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof TwoFactorInterface && $user->isGoogleAuthenticatorEnabled()) {
            $this->session->set('admin_2fa_pending', true);
        }
    }
}
