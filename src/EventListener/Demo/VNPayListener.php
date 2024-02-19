<?php

namespace Starfruit\PaymentBundle\EventListener\Demo;

use Starfruit\PaymentBundle\Event\Model\VNPayEvent;
use Starfruit\PaymentBundle\Controller\Demo\VNPayController;

class VNPayListener
{
    public function findOrder(VNPayEvent $event)
    {
        // find order from transaction data
        $transactionData = $event->getTransactionData();
        $orderId = $transactionData['vnp_TxnRef'];
        $order = $orderId;
        $event->setOrder($order);

        // return order total price
        $event->setAmount(VNPayController::DEMO_AMOUNT);

        // check order status
        $event->setValidStatus(true);
    }

    public function paymentSuccess(VNPayEvent $event)
    {
        try {
            $order = $event->getOrder();
            // do something
            
        } catch (\Throwable $e) {
            
        }
    }

    public function paymentFailure(VNPayEvent $event)
    {
        try {
            $order = $event->getOrder();
            // do something
            
        } catch (\Throwable $e) {
            
        }
    }

    public function paymentCancel(VNPayEvent $event)
    {
        try {
            $order = $event->getOrder();
            // do something
            
        } catch (\Throwable $e) {
            
        }
    }

    public function transactionResponse(VNPayEvent $event)
    {
        try {
            $returnData = $event->getReturnData();
            // do something
            
        } catch (\Throwable $e) {
            
        }
    }
}
