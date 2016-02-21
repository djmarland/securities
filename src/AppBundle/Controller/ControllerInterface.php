<?php
namespace AppBundle\Controller;

/**
 * Thanks to Matt Drollette
 * https://matt.drollette.com/2012/06/calling-a-method-before-every-controller-action-in-symfony2/
 */
use Symfony\Component\HttpFoundation\Request;

interface ControllerInterface
{
    public function initialize(Request $request);
}
