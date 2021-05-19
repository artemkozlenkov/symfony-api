<?php

namespace Webapp\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Webapp\Entity\User;
use Webapp\Kernel;

abstract class WebTestCase extends ApiTestCase
{
    protected static $content;

    /**
     * @var \ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client
     */
    private $client;

    /**
     * @var \ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Response|ResponseInterface
     */
    protected $response;

    protected function setUp(): void
    {
        static::bootKernel(['test_case' => 'TestApp']);
    }

    /**
     * @return \ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client
     */
    protected function makeClient()
    {
        return $this->client = self::createClient(
            [
                'environment' => 'test',
                'test_case' => 'TestApp'
            ]);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $attributes
     * @param array $relationships
     * @param array $headers
     * @param array $arguments
     * @return ResponseInterface
     */
    public function makeRequest(
        string $method,
        string $uri,
        array $attributes = [],
        array $relationships = [],
        array $headers = [],
        array $arguments = []
    ): ResponseInterface
    {
        //Todo check if the base_uri already set in the child class + unit test
        $server = ['base_uri' => 'http://localhost:8001'];

        //Todo check if the server_settings already set in the child class + unit test
        $headers += [
            'Content-Type' => 'application/vnd.api+json',
            'Accept' => 'application/vnd.api+json',
        ];

        $body = [
            'json' => [
                'data' => [
                    'attributes' => $attributes,
                    'relationships' => $relationships
                ]
            ]
        ];

        $response = $this->client->request(strtoupper($method), $uri, array_merge($body, [], $arguments, $server, ['headers' => $headers]));

        if (null == $response) {
            throw new \RuntimeException('The response is null');
        }
        static::$content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $response;
    }

    /**
     * @return string
     */
    protected function generateToken(): string
    {
        $entity = $this->getRepository(User::class)->findOneBy(['email' => 'artem.kozlenkov@gmail.com']);
        $jwtManager = $this->getContainer()->get('lexik_jwt_authentication.jwt_manager');
        return $jwtManager->create($entity);
    }

    /**
     * @throws \RuntimeException
     */
    protected function getContent(): array
    {
        if (null == static::$content || !is_array(static::$content)) {
            throw new \RuntimeException('Content unknown yet.');
        }

        return self::$content;
    }

    protected static function getKernelClass()
    {
        return Kernel::class;
    }

    public function getContainer(): ContainerInterface
    {
        return self::$container;
    }

    /**
     * @throws \ReflectionException
     */
    public static function setUpBeforeClass(): void
    {
        self::removeCacheDir();
        static::ensureKernelShutdown();
        static::$kernel = null;
    }

    /**
     * @throws \ReflectionException
     */
    public static function removeCacheDir()
    {
        $kernel = static::getKernelClass();
        $r = new \ReflectionClass($kernel);

        $root = \dirname($r->getFileName(), 4);

        if (!\is_dir($cacheDir = "$root/var/cache")) {
            // todo log into monolog service throw new \RuntimeException('Theres no cache directory found!');
            return;
        }

        $fs = new Filesystem();
        $fs->remove($cacheDir);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getManager(): EntityManagerInterface
    {
        return $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    protected function getRepository(string $class): ObjectRepository
    {
        return $this->getManager()->getRepository($class);
    }
}
