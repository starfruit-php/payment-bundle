<?php

namespace Starfruit\PaymentBundle\Controller\Demo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;

use Starfruit\PaymentBundle\Service\VNPayService;

/**
 * @Route("/vnpay", name="starfruit-demo-vnpay-")
 */
class VNPayController extends BaseController
{
    /**
     * @Route("/payment-url", name="payment-url")
     */
    public function paymentUrlAction(Request $request)
    {
        $params = []; // change value of any element in payment input data
        $orderInfo = 'Demo payment';
        $amount = self::DEMO_AMOUNT;
        $redirectUrl = $this->generateUrl('starfruit-demo-vnpay-return-url', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $expireMinutes = VNPayService::DEFAULT_EXPIRE_MINUTES;
        $txtRef = time(); // unique value

        $url = VNPayService::generatePaymentUrl($txtRef, $redirectUrl, $amount, $orderInfo, $expireMinutes, $params);

        return new Response('<a href="'. $url .'" target="_blank">Click to continue</a>');
        // return $this->redirect($url);
    }

    /**
     * @Route("/callback", name="callback")
     */
    public function callbackAction(Request $request, ApplicationLogger $logger)
    {
        $requestData = $request->query->all();
        $returnData = VNPayService::processTransaction($requestData);

        // log
        $logger->alert(json_encode($returnData));

        return $this->json($returnData);
    }

    /**
     * @Route("/return-url", name="return-url")
     */
    public function returnUrlAction(Request $request)
    {
        // $requestData = $request->query->all();
        // $returnData = VNPayService::processTransaction($requestData);
        // return $this->json($returnData);

        return new Response('do something to show payment result with realtime to enduser');
    }
}
