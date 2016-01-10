<?php
namespace AppBundle\Extension;

class AssetPathExtension extends \Twig_Extension
{
    private $configPath;

    private $assetsManifest;

    public function __construct($configPath)
    {
        $this->configPath = $configPath;
        $manifest = @file_get_contents($this->configPath . '/assets.json');
        if ($manifest) {
            $this->assetsManifest = json_decode($manifest);
        }
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
        return '/static/dist/' . ($this->assetsManifest->{$asset} ?? $asset);
    }
}