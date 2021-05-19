<?php


namespace Webapp\Event\User;


use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Webapp\Entity\User;

class UserEvent extends GenericEvent
{
    /**
     * UserEvent constructor.
     * @param ResponseEvent $responseEvent
     */
    public function __construct(User $user)
    {
        parent::__construct();
    }
}
