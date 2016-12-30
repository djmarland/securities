<?php

namespace AppBundle\Controller;

use DateTimeImmutable;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CurvesController extends Controller
{
    public function showAction(Request $request)
    {
        $year = $this->request->get('year');
        $month = $this->request->get('month');
        $day = $this->request->get('day');

        if ($year) {
            try {
                $date = new DateTimeImmutable($year . '-' . $month . '-' . $day . 'T00:00Z');
            } catch (Exception $e) {
                // invalid date
                throw new HttpException(400, 'Invalid Date');
            }
        } else {
            $date = $this->get('app.time_provider');
        }

        $curve = $this->get('app.services.curves')->findForDate($date);
        $curve = reset($curve);

        $this->toView('curve', $curve);
        $this->toView('date', $date);
        $this->toView('dateFormatted', $date->format('j M Y'));
        return $this->renderTemplate('curves:show');
    }
}
