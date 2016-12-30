<?php
namespace AppBundle\Extension;

class AssetPathExtension extends \Twig_Extension
{
    private $timestamp;

    public function __construct($configPath)
    {
        $this->timestamp = @file_get_contents($configPath . '/release-time.txt');
        if (!$this->timestamp) {
            $this->timestamp = time();
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
        return '/static/' . $this->timestamp . '/' . $asset;
    }
}
