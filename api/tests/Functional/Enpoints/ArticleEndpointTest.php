<?php

namespace Webapp\Tests\Functional\Enpoints;

use Symfony\Component\HttpFoundation\Response;
use Webapp\Entity\Article;
use Webapp\Entity\User;
use Webapp\Test\DBTestTrait;
use Webapp\Test\WebTestCase;
use Webapp\Tests\Data\ArticleData;

class ArticleEndpointTest extends WebTestCase
{
    use DBTestTrait;

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testGetArticles(): void
    {
        $this->makeClient();

        $token = $this->generateToken();
        $this->makeRequest('GET', 'articles', [], [], ['Authorization' => "Bearer $token"]);

        self::assertResponseStatusCodeSame(200);
    }

    /**
     * @throws \Exception
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testPostArticle(): void
    {
        $this->makeClient();

        $requestBody = ArticleData::getData('article_post');
        $response = $this->makeRequest('POST', '/articles', $requestBody);

        $this->assertEquals($response->getStatusCode(), Response::HTTP_CREATED);

        $manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $repository = $manager->getRepository(Article::class);

        $name = $requestBody['json']['data']['attributes']['name'];

        $entity = $repository->findOneBy(['name' => $name]);
        $manager->refresh($entity);

        $this->assertNotEmpty($entity);
    }

    public function testPutArticle()
    {
        $this->makeClient();

        $requestBody = ArticleData::getData('article_post');

        $response = $this->makeRequest('POST', '/articles', $requestBody);
        $this->assertEquals($response->getStatusCode(), Response::HTTP_CREATED);

        $newName = $requestBody['json']['data']['attributes']['name'] = 'another array';

        unset($response);

        $response = $this->makeRequest('PUT', '/articles/11', $requestBody);
        $this->assertEquals($newName, json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR)['data']['attributes']['name']);
    }

    public function testGetOneArticle()
    {
        $this->makeClient();

        $response = $this->makeRequest('GET', '/articles/1');

        $manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $repository = $manager->getRepository(Article::class);

        /** @var Article $entity */
        $entity = $repository->findOneBy(['id' => '1']);

        $this->assertEquals($entity->getName(), json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR)['data']['attributes']['name']);
    }
}
