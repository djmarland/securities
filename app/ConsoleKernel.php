<?php

require_once __DIR__ . '/AppKernel.php';

class ConsoleKernel extends AppKernel
{
    const CONFIG_FILE = 'console';

    public function registerBundles()
    {
        $bundles = parent::registerBundles();

        $bundles[] = new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle();
        $bundles[] = new ConsoleBundle\ConsoleBundle();

        return $bundles;
    }
}
