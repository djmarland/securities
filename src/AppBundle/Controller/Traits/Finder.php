<?php

namespace AppBundle\Controller\Traits;

use AppBundle\Presenter\Organism\Finder\FinderItemPresenter;
use AppBundle\Presenter\Organism\Finder\FinderPresenter;
use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\Entity\Industry;
use SecuritiesService\Domain\Entity\ParentGroup;
use SecuritiesService\Domain\Entity\Sector;

trait Finder
{
    public function setFinder(
        Industry $industry = null,
        Sector $sector = null,
        ParentGroup $group = null,
        Company $issuer = null
    ) {
        $items = $this->getIndustryItems(
            $industry,
            $sector,
            $group,
            $issuer
        );

        // is the finder in an initial state (nothing selected)?
        $initial = (!$industry && !$sector && !$group && !$issuer);

        $finder = new FinderPresenter($items, $initial);

        $this->toView('finder', $finder);
    }

    private function getIndustryItems(
        Industry $industry = null,
        Sector $sector = null,
        ParentGroup $group = null,
        Company $issuer = null
    ) {
        $industries = $this->get('app.services.industries')
            ->findAll();

        $items = [];
        foreach ($industries as $ind) {
            $title = $ind->getName();
            $url = $this->generateUrl('industry_show', ['industry_id' => $ind->getId()]);
            $children = null;
            $active = false;
            if ($industry && $industry->getId() == $ind->getId()) {
                $children = $this->getSectorItems($industry, $sector, $group, $issuer);
                $active = true;
            }
            $items[] = new FinderItemPresenter($url, $title, $active, 'Sectors', $children);
        }
        return $items;
    }

    private function getSectorItems(
        Industry $industry = null,
        Sector $sector = null,
        ParentGroup $group = null,
        Company $issuer = null
    ) {
        $sectors = $this->get('app.services.sectors')
            ->findAllByIndustry($industry);

        $items = [];
        foreach ($sectors as $sec) {
            $title = $sec->getName();
            $url = $this->generateUrl('sector_show', ['sector_id' => $sec->getId()]);
            $children = null;
            $active = false;
            if ($sector && $sector->getId() == $sec->getId()) {
                $children = $this->getGroupItems($sector, $group, $issuer);
                $active = true;
            }
            $items[] = new FinderItemPresenter($url, $title, $active, 'Groups', $children);
        }
        return $items;
    }

    private function getGroupItems(
        Sector $sector = null,
        ParentGroup $group = null,
        Company $issuer = null
    ) {
        $groups = $this->get('app.services.groups')
            ->findAllBySector($sector);

        $items = [];
        foreach ($groups as $gr) {
            $title = $gr->getName();
            $url = $this->generateUrl('group_show', ['group_id' => $gr->getId()]);
            $children = null;
            $active = false;
            if ($group && $group->getId() == $gr->getId()) {
                $children = $this->getIssuerItems($group, $issuer);
                $active = true;
            }
            $items[] = new FinderItemPresenter($url, $title, $active, 'Issuers', $children);
        }
        return $items;
    }

    private function getIssuerItems(
        ParentGroup $group = null,
        Company $issuer = null
    ) {
        $issuers = $this->get('app.services.issuers')
            ->findAllByGroup($group);

        $items = [];
        foreach ($issuers as $iss) {
            $title = $iss->getName();
            $url = $this->generateUrl('issuer_show', ['issuer_id' => $iss->getId()]);
            $active = false;
            if ($issuer && $issuer->getId() == $iss->getId()) {
                $active = true;
            }
            $items[] = new FinderItemPresenter($url, $title, $active);
        }
        return $items;
    }
}