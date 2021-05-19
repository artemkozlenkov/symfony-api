<?php

namespace Webapp\Tests\Functional\Enpoints;

use Webapp\Entity\User;
use Webapp\Test\DBTestTrait;
use Webapp\Test\WebTestCase;
use Webapp\Tests\Data\UserData;

class UserEndpointTest extends WebTestCase
{
    use DBTestTrait;

    public function testRegisterUser()
    {
        $this->makeClient();

        $data = UserData::getData('user.register.attr');
        $this->makeRequest('post', 'registers', $data);

        $entity = $this->getRepository(User::class)->findOneBy(['firstName' => $data['firstName']]);

        self::assertEquals($entity->getFirstName(), $data['firstName']);
        self::assertEquals(self::$content['email'], $data['email']);
        self::assertResponseStatusCodeSame(201);
    }

    public function testUserLogin()
    {
        $this->makeClient();

        $postData = UserData::getData('user.login.attr');
        $this->makeRequest('post', 'logins', $postData);

        self::assertResponseStatusCodeSame(201);

        $encoder = $this->getContainer()->get('lexik_jwt_authentication.encoder.lcobucci');
        $token = $encoder->decode($this->getContent()['token']);

        self::assertEquals($token['username'], $postData['email']);
    }
}
