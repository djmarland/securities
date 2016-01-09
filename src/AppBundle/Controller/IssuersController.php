<?php

namespace AppBundle\Controller;

use AppBundle\Presenter\Organism\Issuer\IssuerPresenter;
use AppBundle\Presenter\Organism\Security\SecurityPresenter;
use SecuritiesService\Domain\Entity\Company;
use SecuritiesService\Domain\ValueObject\ID;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use DateTimeImmutable;

class IssuersController extends Controller
{
    public function initialize(Request $request)
    {
        parent::initialize($request);
        $this->toView('currentSection', 'issuers');
    }

    public function listAction()
    {
        $perPage = 1500;
        $currentPage = $this->getCurrentPage();

        $result = $this->get('app.services.issuers')
            ->findAndCountAll($perPage, $currentPage);

        $issuerPresenters = [];
        $issuers = $result->getDomainModels();
        if (!empty($issuers)) {
            foreach ($issuers as $issuer) {
                $issuerPresenters[] = new IssuerPresenter($issuer);
            }
        }

        $this->setTitle('Issuers');
        $this->toView('issuers', $issuerPresenters);
        $this->toView('total', $result->getTotal());

        $this->setPagination(
            $result->getTotal(),
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('issuers:list');
    }

    public function showAction(Request $request)
    {
        $issuer = $this->getIssuer($request);

        $perPage = 50;
        $currentPage = $this->getCurrentPage();

        $securitiesService = $this->get('app.services.securities');

        $result = $securitiesService
            ->findAndCountByIssuer($issuer, $perPage, $currentPage);

        $totalRaised = $securitiesService
            ->sumByIssuer($issuer);

        $securityPresenters = [];
        $securities = $result->getDomainModels();
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security);
            }
        }

        $this->toView('activeLine', 'all');
        $this->toView('lines', $this->getLinesForIssuer($issuer));
        $this->toView('totalRaised', number_format($totalRaised));
        $this->toView('securities', $securityPresenters);
        $this->toView('total', $result->getTotal());

        $this->setPagination(
            $result->getTotal(),
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('issuers:show');
    }

    public function securitiesAction(Request $request)
    {
        $issuer = $this->getIssuer($request);

        $line = $this->getLine($request);

        $perPage = 50;
        $currentPage = $this->getCurrentPage();

        $securitiesService = $this->get('app.services.securities');
        $result = $securitiesService
            ->findAndCountByIssuerAndLine($issuer, $line, $perPage, $currentPage);

        $totalRaised = $securitiesService
            ->sumByIssuerAndLine($issuer, $line);

        $securityPresenters = [];
        $securities = $result->getDomainModels();
        if (!empty($securities)) {
            foreach ($securities as $security) {
                $securityPresenters[] = new SecurityPresenter($security);
            }
        }

        $this->toView('activeLine', $line ? $line->getId() : 'all');
        $this->toView('lines', $this->getLinesForIssuer($issuer));
        $this->toView('totalRaised', number_format($totalRaised));
        $this->toView('securities', $securityPresenters);
        $this->toView('total', $result->getTotal());

        $this->setPagination(
            $result->getTotal(),
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('issuers:show');
    }

    public function maturityProfileAction(Request $request)
    {
        $issuer = $this->getIssuer($request);
        return $this->renderTemplate('issuers:maturity-profile');
    }

    public function issuanceAction(Request $request)
    {
        $issuer = $this->getIssuer($request);

        $today = new DateTimeImmutable(); // @todo - use global app time
        $year = $this->getYear($request, $today);
        if (is_null($year)) {
            if ($today->format('m') == 1) {
                // redirect january to last year, as we won't have any data yet
                return $this->redirect(
                    $this->generateUrl(
                        'issuers_issuance',
                        [
                            'issuer_id' => $issuer->getId(),
                            'year' => $today->format('Y')-1
                        ]
                    )
                );
            }
        }

        $lines = $this->getLinesForIssuer($issuer);
        $lineCounts = [];
        $graphData = [
            array_map(function($line) {
                return 'Line ' . $line->getName();
            }, $lines)
        ];
        array_unshift($graphData[0], 'Month');
        $graphData[0][] = (object) [
            'role' => 'annotation'
        ];
        $monthCounts = [];

        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];
        // for each month, count how many of each line type were issued
        $securitiesService = $this->get('app.services.securities');
        foreach($lines as $line) {
            $lineYear = (object) [
                'line' => $line,
                'months' => []
            ];
            foreach ($months as $month => $name) {
                $count = $securitiesService->countByIssuerLineForMonth(
                    $issuer,
                    $line,
                    $year,
                    $month
                );
                $monthCounts[$month][] = $count;
                $lineYear->months[$month] = $count ? $count : '-';
            }
            $lineCounts[] = $lineYear;
        }

        foreach($months as $num => $month) {
            $row = [
                $month
            ];
            $graphData[] = array_merge($row, $monthCounts[$num], ['']);
        }

        $this->toView('months', $months);
        $this->toView('lines', $lines);
        $this->toView('graphData', $graphData);
        $this->toView('lineCounts', $lineCounts);
        $this->toView('years', $this->getYearsForIssuer($issuer)); // @todo
        $this->toView('activeYear', $year);
        return $this->renderTemplate('issuers:issuance');
    }

    private function getYear(Request $request, $today)
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

    private function getLine(Request $request)
    {
        $lineID = $request->get('line_id');
        if (is_null($lineID)) {
            return null;
        }

        $lineParamInt = (int) $lineID;
        if ($lineID !== (string) $lineParamInt ||
            $lineParamInt <= 0) {
            throw new HttpException(404, 'Invalid ID');
        }

        $result = $this->get('app.services.lines')
            ->findById(new ID((int) $lineParamInt));
        if (!$result->hasResult()) {
            throw new HttpException(404, 'Line ' . $lineID . ' does not exist.');
        }
        return $result->getDomainModel();
    }

    private function getIssuer(Request $request)
    {
        $id = $request->get('issuer_id');

        if ($id !== (string) (int) $id) {
            throw new HttpException(404, 'Invalid ID');
        }

        $result = $this->get('app.services.issuers')
            ->findByID(new ID((int) $id));

        if (!$result->hasResult()) {
            throw new HttpException(404, 'Issuer ' . $id . ' does not exist.');
        }
        $issuer = $result->getDomainModel();

        $this->setTitle($issuer->getName());
        $this->toView('issuer', $issuer);
        return $issuer;
    }

    private function getYearsForIssuer(Company $issuer): array
    {
        // @todo - calculate valid years for this issuer
        return [
            2016, 2015, 2014, 2013, 2012
        ];
    }

    private function getLinesForIssuer(Company $issuer): array
    {
        $result = $this->get('app.services.lines')->findAllByIssuer($issuer);
        return $result->getDomainModels();
    }
}
