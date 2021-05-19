<?php


namespace Webapp\Test;


use Hautelook\AliceBundle\PhpUnit\BaseDatabaseTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webapp\Tests\Functional\App\TestKernel;

trait DBTestTrait
{
    use BaseDatabaseTrait;

    public function setUp(): void
    {
        parent::setUp();
        self::populateDatabase();
    }
}
