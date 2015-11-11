<?php

namespace AppBundle\Service;

class SettingsService extends Service
{
    /**
     * @return ServiceResult|ServiceResultEmpty
     */
    public function get()
    {
        $result = $this->getQueryFactory()
            ->createSettingsQuery()
            ->get();
        if ($result) {
            return new ServiceResult($result);
        }
        return new ServiceResultEmpty();
    }
}
