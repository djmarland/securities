<?php

namespace AppBundle\Controller;

use AppBundle\Presenter\MasterPresenter;
use AppBundle\Presenter\Organism\Pagination\PaginationPresenter;
use AppBundle\Security\Visitor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller extends BaseController implements ControllerInterface
{
    /**
     * @var int
     */
    protected $currentPage = 1;

    /**
     * @var MasterPresenter
     */
    public $masterViewPresenter;

    /**
     * @var Request
     */
    public $request;

    /**
     * Setup common tasks for a controller
     * @param Request $request
     */
    public function initialize(Request $request)
    {
        $this->request = $request;
        $this->appConfig = $this->getParameter('app.config');
        $this->masterViewPresenter = new MasterPresenter($this->appConfig);
        $this->toView('currentYear', date("Y"));
        $this->toView('currentSection', null);
        $this->setSearchContext();
    }

    protected function getCurrentPage()
    {
        $page = $this->request->get('page', 1);

        // must be an integer string
        if (
            strval(intval($page)) !== strval($page) ||
            $page < 1
        ) {
            throw new HttpException(404, 'No such page value');
        }
        return (int)$page;
    }

    protected function setSearchContext()
    {
        $search = $this->request->get('q', null);
        $this->toView('searchContext', $search);
        $this->toView('searchAutofocus', null);
    }

    /**
     * @param int $total Total Results
     * @param int $currentPage The current page value
     * @param int $perPage How many per page
     */
    protected function setPagination(
        $total,
        $currentPage,
        $perPage
    ) {

        $pagination = new PaginationPresenter(
            $total,
            $currentPage,
            $perPage
        );

        if (!$pagination->isValid()) {
            throw new HttpException(404, 'There are not this many pages');
        }

        $this->toView('pagination', $pagination);
    }

    public function toView(
        string $key,
        $value,
        $inFeed = true
    ): Controller {
        $this->masterViewPresenter->set($key, $value, $inFeed);
        return $this;
    }

    /**
     * @param $key
     * @return mixed
     * @throws \AppBundle\Domain\Exception\DataNotSetException
     */
    public function fromView($key)
    {
        return $this->masterViewPresenter->get($key);
    }


    public function setTitle(string $title): Controller
    {
        $this->masterViewPresenter->setTitle($title);
        return $this;
    }

    protected function renderTemplate($template)
    {
        $format = $this->request->get('format', null);
        if ($format == 'json') {
            return new JsonResponse($this->masterViewPresenter->getFeedData());
        }

        $ext = 'html';
        if (in_array($format, ['inc'])) {
            $ext = $format;
        } elseif ($format) {
            throw new HttpException(404, 'Invalid Format');
        }

        $path = 'AppBundle:' . $template . '.' . $ext . '.twig';
        return $this->render($path, $this->masterViewPresenter->getData());
    }

    protected function getYear(Request $request, $today)
    {
        $year = $request->get('year');
        if (is_null($year)) {
            return null;
        }
        $yearInt = (int) $year;
        $thisYear = $today->format('Y');
        if ($year !== (string) $yearInt ||
            $yearInt <= 1900 ||
            $yearInt > $thisYear) {
            throw new HttpException(404, 'Invalid Year: ' . $year);
        }
        return $year;
    }
}
