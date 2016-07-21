<?php

namespace AppBundle\Controller;

use ConsoleBundle\Command\ImportCommand;
use SecuritiesService\Domain\Entity\Security;
use SecuritiesService\Domain\ValueObject\UUID;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AccountController extends Controller
{
    protected $cacheTime = null;

    public function indexAction(Request $request)
    {
        $this->toView('activeTab', 'dashboard');

        return $this->renderTemplate('account:index');
    }

    public function settingsAction(Request $request)
    {
        $this->toView('activeTab', 'settings');

        return $this->renderTemplate('account:settings');
    }
}
