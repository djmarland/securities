<?php

namespace AppBundle\Controller;

use AppBundle\Presenter\MasterPresenter;
use AppBundle\Presenter\Organism\Pagination\PaginationPresenter;
use AppBundle\Security\Visitor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
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
        $this->masterViewPresenter = new MasterPresenter();
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
        return (int) $page;
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

    /**
     * Set values that make it to the view
     * @param $key
     * @param $value
     * @param bool  $inFeed
     * @return $this
     */
    public function toView(
        $key,
        $value,
        $inFeed = true
    ) {
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

    protected function renderTemplate($template)
    {
        $path = 'AppBundle:' . $template . '.html.twig';
        return $this->render($path, $this->masterViewPresenter->getData());
    }
}
