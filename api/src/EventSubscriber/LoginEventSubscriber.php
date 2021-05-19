<?php


namespace Webapp\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Webapp\Event\UserLoginEvent;

class LoginEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private string $token = '';

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -1500],
            UserLoginEvent::USER_LOGIN => 'onUserLogin',
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if(empty($this->token)) return;

        $event->getResponse()->headers->add(['Authorization' => 'Bearer '. $this->token]);
    }

    public function onUserLogin(UserLoginEvent $event)
    {
        $this->token = $event->getToken();
    }
}
