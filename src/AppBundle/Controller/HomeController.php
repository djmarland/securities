<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function indexAction()
    {
        $this->toView('searchAutofocus', 'autofocus');

        $securitiesCount = $this->get('app.services.securities')->countAll();
        $issuersCount = $this->get('app.services.issuers')->countAll();
        $productCounts = $this->get('app.services.securities')->countsByProduct();

        $this->toView('securitiesCount', number_format($securitiesCount));
        $this->toView('issuersCount', number_format($issuersCount));

        $byProduct = [['Funding Product', 'Number']];
        foreach ($productCounts as $pc) {
            $byProduct[] = [
                $pc->product->getName(),
                $pc->count,
            ];
        }
        $this->masterViewPresenter->setFullTitle('ISIN Analytics - The Gateway to London\'s Debt Capital Markets');
        $this->toView('byProduct', $byProduct);
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
        return $this->renderTemplate('home:about');
    }

    public function termsAction()
    {
        return $this->renderTemplate('home:terms');
    }

    public function privacyAction()
    {
        return $this->renderTemplate('home:privacy');
    }

    public function styleguideAction()
    {
        return $this->renderTemplate('home:styleguide');
    }
}
