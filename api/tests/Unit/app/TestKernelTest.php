<?php


namespace WebApp\Tests\Unit\app;


use PHPUnit\Framework\TestCase;
//use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Webapp\Kernel;
use Webapp\Tests\Functional\App\TestKernel;

class TestKernelTest extends TestCase
{
    /**
     * @var TestKernel
     */
    private $testKernel;

    /**
     * TestKernelTest Constructor
     */
    protected function setUp(): void
    {
        $this->testKernel = new TestKernel('test', true);
        parent::setUp();
    }

    public function testGetProjectDir()
    {
        $r = new \ReflectionClass(TestKernel::class);
        $this->assertFileExists($dir = $r->getFileName());

        $this->assertEquals($this->testKernel->getProjectDir(), dirname($dir));
    }

    public function testGetMainDir()
    {
        $r = new \ReflectionClass(Kernel::class);
        $this->assertFileExists($realProjDir = $r->getFileName());

        $dir = \dirname($realProjDir, 2);
        $this->assertFileExists($dir.'/composer.json');
        $this->assertEquals($dir, $this->testKernel->getMainDir());
    }

    public function testGetCacheDir()
    {
        $r = new \ReflectionClass(Kernel::class);
        $dir = \dirname($r->getFileName(),2);
        $this->assertEquals($this->testKernel->getCacheDir(), $dir.'/var/test');
    }
}
