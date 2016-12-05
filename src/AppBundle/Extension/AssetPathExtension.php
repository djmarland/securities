<?php
namespace AppBundle\Extension;

class AssetPathExtension extends \Twig_Extension
{
    private $configPath;

    private $assetsManifest;

    public function __construct($configPath)
    {
        $this->configPath = $configPath;
        $this->assetsManifest = array_merge(
            $this->getManifest('assets-css'),
            $this->getManifest('assets-images'),
            $this->getManifest('assets-js-admin'),
            $this->getManifest('assets-js-app')
        );
    }

    public function getName()
    {
        return 'asset_path';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('asset_path', array($this, 'assetPath')),
        );
    }

    public function assetPath($asset)
    {
        return '/static/dist/' . ($this->assetsManifest[$asset] ?? $asset);
    }

    private function getManifest($name)
    {
        $manifest = @file_get_contents($this->configPath . '/' . $name . '.json');
        if ($manifest) {
            return json_decode($manifest, true);
        }
        return [];
    }
}
