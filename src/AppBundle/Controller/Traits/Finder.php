<?php

namespace AppBundle\Controller\Traits;

use AppBundle\Presenter\Organism\Finder\FinderItemPresenter;
use AppBundle\Presenter\Organism\Finder\FinderPresenter;
use SecuritiesService\Domain\Entity\Industry;

trait Finder
{
    public function setFinder(
        Industry $industry = null
    ) {



        $sectors = null;
        if ($industry) {
            // need to get sectors
            $sectors = $this->get('app.services.sectors')
                ->findAllByIndustry($industry)
                ->getDomainModels();
        }

        // at the top level it's just industries
        $industries = $this->get('app.services.industries')
            ->findAll()
            ->getDomainModels();

        $items = [];
        foreach ($industries as $ind) {
            $title = $ind->getName();
            $url = $this->generateUrl('industries_show', ['industry_id' => $ind->getId()]);
            $children = null;
            if ($industry && $industry->getId() == $ind->getId() && !empty($sectors)) {
                $children = [];
                foreach ($sectors as $sector) {
                    $children[] = $this->getSectorItem($sector);
                }
            }

            $items[] = new FinderItemPresenter($url, $title, $children);
        }

        $finder = new FinderPresenter($items);

        $this->toView('finder', $finder);
    }

    private function getSectorItem($sector)
    {
        $title = $sector->getName();
        $url = $this->generateUrl('sectors_show', ['sector_id' => $sector->getId()]);
        return new FinderItemPresenter($url, $title);
    }
}