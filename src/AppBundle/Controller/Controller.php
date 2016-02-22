<?php

namespace AppBundle\Controller;

use AppBundle\Presenter\MasterPresenter;
use AppBundle\Presenter\Organism\Pagination\PaginationPresenter;
use AppBundle\Security\Visitor;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller extends BaseController implements ControllerInterface
{

    /** @var MasterPresenter */
    public $masterViewPresenter;

    /** @var Request */
    public $request;

    protected $currentPage = 1;
    protected $appConfig;

    private $applicationTime;

    /** Setup common tasks for a controller */
    public function initialize(Request $request)
    {
        $this->request = $request;
        $this->appConfig = $this->getParameter('app.config');
        $this->masterViewPresenter = new MasterPresenter($this->appConfig);
        $this->applicationTime = new DateTimeImmutable(); // @todo - allow this to be set/overridden
        $this->toView('currentYear', date("Y"));
        $this->toView('currentSection', null);
        $this->setSearchContext();
    }


    public function toView(
        string $key,
        $value,
        $inFeed = true
    ): Controller {
        $this->masterViewPresenter->set($key, $value, $inFeed);
        return $this;
    }

    public function fromView(string $key)
    {
        return $this->masterViewPresenter->get($key);
    }

    public function setTitle(string $title): Controller
    {
        $this->masterViewPresenter->setTitle($title);
        return $this;
    }

    protected function getApplicationTime(): DateTimeImmutable
    {
        return $this->applicationTime;
    }

    protected function getCurrentPage(): int
    {
        $page = $this->request->get('page', 1);

        // must be an integer string
        if (strval(intval($page)) !== strval($page) ||
            $page < 1
        ) {
            throw new HttpException(404, 'No such page value');
        }
        return (int) $page;
    }

    protected function setSearchContext()
    {
        $search = $this->request->get('q', null);
        $this->toView('searchContext', $search);
        $this->toView('searchAutofocus', null);
    }

    protected function setPagination(
        int $total,
        int $currentPage,
        int $perPage
    ) {

        $pagination = new PaginationPresenter(
            $total,
            $currentPage,
            $perPage,
            ['hrefPrefix' => '?' . $this->request->getQueryString()]
        );

        if (!$pagination->isValid()) {
            throw new HttpException(404, 'There are not this many pages');
        }

        $this->toView('pagination', $pagination);
    }

    protected function setCacheHeaders($response)
    {
        $response->setPublic();

        $response->setMaxAge(600);
        $response->setSharedMaxAge(600);
    }

    protected function renderTemplate($template)
    {
        $format = $this->request->get('format', null);
        if ($format == 'json') {
            $response = new JsonResponse($this->masterViewPresenter->getFeedData());
            $this->setCacheHeaders($response);
            return $response;
        }

        $ext = 'html';
        if (in_array($format, ['inc'])) {
            $ext = $format;
        } elseif ($format) {
            throw new HttpException(404, 'Invalid Format');
        }

        $response = new Response();
        $this->setCacheHeaders($response);

        $path = 'AppBundle:' . $template . '.' . $ext . '.twig';
        return $this->render($path, $this->masterViewPresenter->getData(), $response);
    }

    protected function getYear(Request $request, DateTimeImmutable $today)
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
