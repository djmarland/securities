<?php

namespace AppBundle\Controller;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ErrorController extends ExceptionController
{
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $meta = [
            'fullTitle' => 'Error - ISIN Analytics',
            'siteTitle' => 'ISIN Analytics',
            'isOk' => false,
            'environment' => 'live',
        ];

        $code = $exception->getStatusCode();
        $template = 'error';
        switch ($code) {
            case 404:
                $template = 'error404';
                break;
            case 403:
                $template = 'error403';
                break;
        }

        return new Response($this->twig->render(
            'AppBundle:error:' . $template . '.html.twig',
            [
                'status_code' => $code,
                'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                'exception' => $exception,
                'logger' => $logger,
                'currentContent' => '',
                'meta' => $meta,
                'pagination' => null,
                'searchContext' => '',
                'searchAutofocus' => '',
                'currentYear' => '',
                'adverts' => [
                    'areActive' => false,
                ],
            ]
        ));
    }
}
