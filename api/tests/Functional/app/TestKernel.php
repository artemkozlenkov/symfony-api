<?php

namespace Webapp\Tests\Functional\app;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Webapp\Kernel as KernelAlias;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    public function getMainDir(): string
    {
        $r = new \ReflectionClass(KernelAlias::class);

        return \dirname($r->getFileName(), 2);
    }

    public function getName()
    {
        $r = new \ReflectionClass(KernelAlias::class);

        return $r->getNamespaceName();
    }

    public function getProjectDir(): string
    {
        $r = new \ReflectionClass(__CLASS__);

        return \dirname($r->getFileName());
    }

    public function getCacheDir()
    {
        return $this->getMainDir().'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return $this->getMainDir().'/var/log/'.$this->getEnvironment();
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', true);
        $container->setParameter('main_dir', $this->getMainDir());

        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/'.'/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');
    }

    /**
     * Add or import routes into your application.
     *
     * @throws \Symfony\Component\Config\Exception\LoaderLoadException
     */
    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');
    }
}
