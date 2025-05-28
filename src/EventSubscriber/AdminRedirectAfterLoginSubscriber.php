<?php

namespace VivanWebSolution\Sylius2FAPlugin\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class AdminRedirectAfterLoginSubscriber implements EventSubscriberInterface
{
    private $router;
    private $session;

    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->session = $requestStack->getSession();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        if ($this->session->has('admin_2fa_pending') && $this->session->get('admin_2fa_pending')) {
            $this->session->remove('admin_2fa_pending');
            $response = new RedirectResponse($this->router->generate('admin_2fa_verify_code'));
            $event->setResponse($response);
        }
    }
}
