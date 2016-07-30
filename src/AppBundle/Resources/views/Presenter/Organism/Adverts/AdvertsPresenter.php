<?php

namespace AppBundle\Presenter\Organism\Adverts;

use AppBundle\Presenter\Presenter;

class AdvertsPresenter extends Presenter implements AdvertsPresenterInterface
{
    const CLIENT_ID = 'ca-pub-4717500607987181';

    const SLOT_IDS = [
        'overview' => '2419457559',
        'isin' => '4256117558',
        'homepage-mid' => '4923359556',
        'homepage-side' => '6400092752',
    ];

    protected $options = [
        'active' => true,
        'disabled' => false
    ];

    private $currentVariant = 'overview';


    public function __construct(
        $options = []
    ) {
        parent::__construct(null, $options);
        if ($this->options['disabled']) {
            $this->setTemplateVariation('disabled');
        }
    }

    public function areActive(): bool
    {
        if (isset($this->options['active'])) {
            return $this->options['active'];
        }
        return $this->options;
    }

    public function getVariant(): string
    {
        return $this->currentVariant;
    }

    public function getVariantVars(string $variant = null): array
    {
        if ($variant) {
            $this->currentVariant = $variant;
        }
        return $this->getVars();
    }

    public function getClientId(): string
    {
        return self::CLIENT_ID;
    }

    public function getSlotId(): string
    {
        $slots = self::SLOT_IDS;
        if (isset($slots[$this->currentVariant])) {
            return $slots[$this->currentVariant];
        }
        return '';
    }
}

