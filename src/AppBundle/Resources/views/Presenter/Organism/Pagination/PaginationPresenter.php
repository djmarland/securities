<?php

namespace AppBundle\Presenter\Organism\Pagination;

use AppBundle\Presenter\Presenter;

/**
 * Class CustomerPresenter
 * An object for displaying a customer object
 */
class PaginationPresenter extends Presenter implements PaginationPresenterInterface
{
    protected $options = [
        'hrefPrefix' => null,
    ];
    private $total;
    private $currentPage;
    private $perPage;
    private $totalPages;

    public function __construct(
        int $total,
        int $currentPage,
        int $perPage,
        array $options = []
    ) {
        parent::__construct(null, $options);

        $this->total = $total;
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;

        $this->calculate();
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return (
            $this->currentPage > 0 &&
            $this->currentPage <= $this->totalPages
        );
    }

    public function getPrevUrl()
    {
        if ($this->currentPage == 1) {
            return null;
        }
        return $this->generateUrl($this->currentPage - 1);
    }

    public function getNextUrl()
    {
        if ($this->currentPage == $this->totalPages) {
            return null;
        }
        return $this->generateUrl($this->currentPage + 1);
    }

    public function getIsActive()
    {
        return ($this->totalPages > 1);
    }

    public function getShowStatus()
    {
        return ($this->total > 0);
    }

    public function getStart()
    {
        return $this->getDisplayNumber((($this->currentPage - 1) * $this->perPage) + 1);
    }

    public function getEnd()
    {
        $end = $this->perPage * $this->currentPage;
        if ($end > $this->total) {
            $end = $this->total;
        }
        return $this->getDisplayNumber($end);
    }

    public function getTotal()
    {
        return $this->getDisplayNumber($this->total);
    }

    private function getDisplayNumber($number)
    {
        return number_format($number);
    }

    private function calculate()
    {
        $this->totalPages = (int) ceil($this->total / $this->perPage);
        if ($this->totalPages < 1) {
            // always at least one page
            $this->totalPages = 1;
        }
    }

    private function generateUrl(int $page): string
    {
        $queryParts = [];
        $prefix = '';
        $path = '';
        // check if there already is a query string
        if (!empty($this->options['hrefPrefix'])) {
            $parts = explode('?', $this->options['hrefPrefix']);
            $prefix = $parts[0];
            if (isset($parts[1]) && !empty($parts[1])) {
                parse_str($parts[1], $queryParts);
                // remove any page params to set them later
                if (isset($queryParts['page'])) {
                    unset($queryParts['page']);
                }
            }
        }
        // first page doesn't have the query string
        // (to prevent duplicate URLs)
        if ($page !== 1) {
            $queryParts['page'] = $page;
        }
        if (!empty($queryParts)) {
            $path = '?' . http_build_query($queryParts);
        }
        $url = $prefix . $path;
        if (empty($url)) {
            $url = '?';
        }
        return $url;
    }
}
