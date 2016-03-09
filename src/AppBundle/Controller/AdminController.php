<?php

namespace AppBundle\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {

        return $this->renderTemplate('admin:index');
    }
}
