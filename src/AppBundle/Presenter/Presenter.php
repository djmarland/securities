<?php

namespace AppBundle\Presenter;

/**
 * Class Presenter
 * For those which the base Presenter classes inherit (for view logic)
 */
abstract class Presenter
{
    const TWIG_SUFFIX = '.html.twig';

    /**
     * A unique generated ID for this object
     * Only relevant for page renders that need a reference
     *
     * @var
     */
    protected $uniqueId;
    protected $domainModel;
    protected $options = [
        'classType' => 'Presenter',
    ];

    /**
     * @var string
     * Suffix for template, if there is a variation
     * Call setTemplateVariation in child class to setup
     */
    private $templateVariantSuffix = '';

    public function __construct(
        $domainModel = null,
        $options = []
    ) {
        $this->domainModel = $domainModel;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Convert the options to an object and return
     */
    public function getOptions(): \stdClass
    {
        return (object) $this->options;
    }

    /**
     * Get the base property that the twig template will be expecting
     * to find it's variables under. Matches the presenter name (e.g item).
     * Therefore the Twig template would use {{ item.title }}
     */
    public function getBase(): string
    {
        $classPath = $this->getClassPath();
        $parts = explode('/', $classPath);
        return end($parts);
    }

    public function getVars(): array
    {
        return [
            $this->getBase() => $this,
        ];
    }

    /**
     * Get or generate a unique ID. Once generated once the same one will be used
     * Only used for unique references in a single render
     */
    public function getUniqueID(): string
    {
        if (!$this->uniqueId) {
            $parts = explode('\\', get_called_class());

            $class = end($parts);
            $this->uniqueId = 'View-' . $class . '-' . mt_rand();
        }
        return $this->uniqueId;
    }

    /**
     * Full path to the twig template the presenter class renders
     */
    public function getTemplatePath(): string
    {
        return '@' . $this->getClassPath() . $this->templateVariantSuffix . self::TWIG_SUFFIX;
    }

    /**
     * Set the twig template suffix
     * This can be used by Presenter classes in case they need to
     * suffix their own variations. item-reversed for example
     * would be set by calling setTemplateSuffix('reversed')
     * The dash separation is added automatically
     */
    protected function setTemplateVariation($templateVariantSuffix = ''): string
    {
        if (empty($templateVariantSuffix)) {
            $this->templateVariantSuffix = '';
        }
        $this->templateVariantSuffix = '-' . $templateVariantSuffix;
    }

    /**
     * Auto calculates the path to the Presenter Class
     * So that every presenter doesn't need to restate its template path and base
     */
    private function getClassPath(): string
    {
        $className = get_called_class();
        // strip off the namespace
        $classPath = str_replace('App\Presenter\\', '', $className);
        // split by backslash
        $parts = explode('\\', $classPath);
        // get the last bit (the class name)
        $last = array_pop($parts);
        // Trim the class name from the word 'Presenter'
        $last = substr($last, 0, strpos($last, 'Presenter'));
        // Add the class name back to the parts (lower cased as that's how we need it)
        $parts[] = strtolower($last);
        // Recombobulate with forward slashes
        return implode('/', $parts);
    }
}
