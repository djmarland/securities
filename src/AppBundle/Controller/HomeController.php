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

        $byProduct = [['Funding Product', 'Number']];
        foreach ($productCounts as $pc) {
            $byProduct[] = [
                $pc->product->getName(),
                $pc->count,
            ];
        }
        $this->toView('byProduct', $byProduct);
        $this->toView('securities', null);

        if ($this->appConfig->featureIsActive(Features::RECENT_ISSUANCE_ON_HOMEPAGE())) {
            $latestIssuance = $securitiesService->findLatestIssuance(5);
            $this->toView('securities', $this->securitiesToPresenters($latestIssuance));
        }

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
