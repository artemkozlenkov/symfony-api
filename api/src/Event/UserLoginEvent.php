<?php


namespace Webapp\Event;


use Symfony\Contracts\EventDispatcher\Event;

class UserLoginEvent extends Event
{
    public const USER_LOGIN = 'user.login';

    /**
     * @var string
     */
    private $token;

    /**
     * UserLoginEvent constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return UserLoginEvent
     */
    public function setToken(string $token): UserLoginEvent
    {
        $this->token = $token;
        return $this;
    }
}
