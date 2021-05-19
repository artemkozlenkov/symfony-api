<?php


namespace Webapp\Tests\Data;


class UserData
{
    public static function getData(string $id): array
    {
        $data = [
            'user.register.attr' => [
                'firstName' => 'SuperUser',
                'lastName' => 'SuperUser',
                'email' => 'super.user@gmail.com',
                'password' => 'super111',
            ],
            'user.login.attr' => [
                'email' => 'artem.kozlenkov@gmail.com',
                'password' => 'artem1902',
            ],
        ];

        if ($id == null) {
            throw new \RuntimeException("theres no such id");
        }

        return $data[$id];
    }
}
