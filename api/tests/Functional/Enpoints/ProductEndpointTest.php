<?php

namespace Webapp\Tests\Functional\Enpoints;

use Doctrine\ORM\EntityRepository;
use Webapp\Entity\Product;
use Webapp\Test\DBTestTrait;
use Webapp\Test\WebTestCase;
use Webapp\Tests\Data\ProductData;

class ProductEndpointTest extends WebTestCase
{
    use DBTestTrait;

    public function testGetProducts(): void
    {
        $this->makeClient();
        $this->makeRequest('GET', '/products');

        self::assertResponseIsSuccessful();
    }

    public function testGetOneProduct(): void
    {
        $this->makeClient();
        $id = 1;
        $this->makeRequest('GET', sprintf('products/%d', $id));

        $manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        /** @var EntityRepository $repository */
        $repository = $manager->getRepository(Product::class);

        $entity = $repository->findOneBy(['id' => $id]);

        $this->assertEquals(
            $entity->getName(),
            $this->getContent()['data']['attributes']['name']
        );
    }

    public function testPostOneProduct(): void
    {
        $this->makeClient();
        $data = ProductData::getData('product_post');

        $this->makeRequest('POST', '/products', $data);
        self::assertResponseIsSuccessful();
    }
}
