<?php

namespace Starfruit\PaymentBundle\Event;

final class VNPayEvents
{
    /**
     * @Event("Starfruit\PaymentBundle\Event\Model\VNPayEvent")
     *
     * @var string
     * 
     * Find order infor from transaction data.
     */
    const FIND_ORDER = 'starfruit.payment.vnpay.findOrder';

    /**
     * @Event("Starfruit\PaymentBundle\Event\Model\VNPayEvent")
     *
     * @var string
     * 
     * Do something after payment status is success.
     */
    const PAYMENT_SUCCESS = 'starfruit.payment.vnpay.paymentSuccess';

    /**
     * @Event("Starfruit\PaymentBundle\Event\Model\VNPayEvent")
     *
     * @var string
     * 
     * Do something after payment status is failure.
     */
    const PAYMENT_FAILURE = 'starfruit.payment.vnpay.paymentFailure';

    /**
     * @Event("Starfruit\PaymentBundle\Event\Model\VNPayEvent")
     *
     * @var string
     * 
     * Do something after payment status is cancel.
     */
    const PAYMENT_CANCEL = 'starfruit.payment.vnpay.paymentCancel';

    /**
     * @Event("Starfruit\PaymentBundle\Event\Model\VNPayEvent")
     *
     * @var string
     * 
     * Do something after transaction is invalid.
     */
    const TRANSACTION_RESPONSE = 'starfruit.payment.vnpay.transactionResponse';
}
