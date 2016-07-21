<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    const CONFIG_FILE = 'config';

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new ConsoleBundle\ConsoleBundle(),
            new AppBundle\AppBundle()
        );

        if (in_array($this->getEnvironment(), array('dev','alpha'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $suffix = '';
        $env = $this->getEnvironment();
        if (in_array($env, ['dev','alpha','beta'])) {
            $suffix = '_' . $env;
        }
        $loader->load($this->getRootDir().'/config/' . static::CONFIG_FILE . $suffix . '.yml');
    }
}
