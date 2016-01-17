<?php

namespace AppBundle\Controller;

class ReportsController extends Controller
{
    public function listAction()
    {
        return $this->renderTemplate('reports:list');
    }

    public function fsa54Action()
    {

        $this->toView('graphData', [
            ['Currency', '2015', '2014'],
            ['USD', 1000, 400],
            ['GBP', 1170, 460],
            ['EUR', 660, 1120]
        ]);
        return $this->renderTemplate('reports:fsa54');
    }
}