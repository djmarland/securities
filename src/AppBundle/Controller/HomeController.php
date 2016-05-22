<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Traits\SecuritiesTrait;
use SecuritiesService\Domain\Entity\Enum\Features;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    use SecuritiesTrait;

    public function indexAction()
    {
        $this->toView('searchAutofocus', 'autofocus');
        $this->masterViewPresenter->setFullTitle(
            $this->appConfig->getSiteTitle() . ' - ' . $this->appConfig->getSiteTagLine()
        );

        $securitiesService = $this->get('app.services.securities');

        $securitiesCount = $securitiesService->count();
        $issuersCount = $this->get('app.services.issuers')->countAll();
        $productCounts = $securitiesService->countsByProduct();

        $this->toView('securitiesCount', number_format($securitiesCount));
        $this->toView('issuersCount', number_format($issuersCount));

        $colours = [
            "#634D7B", "#B66D6D", "#B6B16D", "#579157", '#777', "#342638"
        ];
        $products = [];
        $productDataset = [];
        $headings = [];
        foreach ($productCounts as $i => $pc) {
            $products[] = [
                'product' => $pc->product,
                'colour' => $colours[$i],
            ];
            $headings[] = $pc->product->getName();
            $productDataset[] = $pc->count;
        }
        $this->toView('chartColours', $colours);
        $this->toView('chartHeadings', $headings);
        $this->toView('productsDataset', $productDataset);
        $this->toView('products', $products);
        $this->toView('securities', null);

        if ($this->appConfig->featureIsActive(Features::RECENT_ISSUANCE_ON_HOMEPAGE())) {
            $latestIssuance = $securitiesService->findLatestIssuance(5);
            $this->toView('securities', $this->securitiesToPresenters($latestIssuance));
        }

        // top 10 currencies, year to date
        $now = new \DateTimeImmutable();
        $startOfYear = new \DateTimeImmutable($now->format('Y') . '-01-01T00:00:00Z');

        $top10Currencies = $securitiesService->sumByCurrencyForDateRange($startOfYear, $now, 5);
        $currencyChartHeadings = [];
        $currencyChartData = [];
        foreach ($top10Currencies as $c) {
            $currencyChartHeadings[] = $c->currency->getCode();
            $currencyChartData[] = $c->total;
        }

        $this->toView('currencyChartHeadings', $currencyChartHeadings);
        $this->toView('currencyChartData', $currencyChartData);

        $top10Industries = $securitiesService->sumByIndustryForDateRange($startOfYear, $now, 5);
        $industryChartHeadings = [];
        $industryChartData = [];
        foreach ($top10Industries as $c) {
            $industryChartHeadings[] = $c->industry->getName();
            $industryChartData[] = $c->total;
        }

        $this->toView('industryChartHeadings', $industryChartHeadings);
        $this->toView('industryChartData', $industryChartData);

        return $this->renderTemplate('home:index');
    }

    public function sitemapAction()
    {
        $prefix = 'http://www.isinanalytics.com';
        $urls = [];


        // all securities
        $securities = $this->get('app.services.securities')
            ->findAllSimple();

        foreach ($securities as $security) {
            $urls[] = $prefix . $this->generateUrl(
                'security_show',
                ['isin' => (string) $security->getISIN()]
            );
        }

        // all issuers
        $issuers = $this->get('app.services.issuers')
            ->findAllSimple();

        foreach ($issuers as $issuer) {
            $urls[] = $prefix . $this->generateUrl(
                'issuer_show',
                ['issuer_id' => $issuer->getUrlKey()]
            );
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'xml');
        $this->setCacheHeaders($response);

        $path = 'AppBundle:sitemap.xml.twig';
        return $this->render($path, ['urls' => $urls], $response);
    }

    public function aboutAction()
    {
        $this->setTitle('About');
        return $this->renderTemplate('home:about');
    }

    public function termsAction()
    {
        $this->setTitle('Terms of Use');
        return $this->renderTemplate('home:terms');
    }

    public function privacyAction()
    {
        $this->setTitle('Privacy Policy');
        return $this->renderTemplate('home:privacy');
    }

    public function styleguideAction()
    {
        return $this->renderTemplate('home:styleguide');
    }
}
