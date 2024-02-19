<?php

namespace Starfruit\PaymentBundle\Service;

use Starfruit\PaymentBundle\Event\VNPayEvents;
use Starfruit\PaymentBundle\Event\Model\VNPayEvent;

class VNPayService extends BaseService
{
    const COMMAND = 'pay';
    const CURRENCY_CODE = 'VND';
    const ORDER_TYPE = '250000';
    const HASH_CODE = 'sha512';
    const DATE_FORMAT = 'YmdHis';

    const ENV_PAYMENT_URL = 'VNP_PAYMENT_URL';
    const ENV_TMNCODE = 'VNP_TMNCODE';
    const ENV_HASH_SECRET = 'VNP_HASH_SECRET';
    const ENV_VERSION = 'VNP_VERSION';

    const TRANS_AMOUNT_MULTIPLIER = 100;
    const TRANS_SUCCESS_CODE = '00';
    const TRANS_CANCEL_CODE = '24';

    const DEFAULT_EXPIRE_MINUTES = 10;

    /**
     * Generate a link to redirect payment view.
     * 
     * @param string $txtRef - unique reference code
     * @param string $redirectUrl - url is redirected after payment complete
     * @param string $amount - order total price
     * @param string $orderInfo - order description
     * @param array $params - change value in payment url
     * 
     * @return string 
     */
    public static function generatePaymentUrl(
        string $txtRef,
        string $redirectUrl,
        int $amount,
        string $orderInfo,
        int $expireMinutes = self::DEFAULT_EXPIRE_MINUTES,
        array $params = []
    )
    {
        $vnp_Url = getenv(self::ENV_PAYMENT_URL) ?: $_ENV[self::ENV_PAYMENT_URL];
        $vnp_TmnCode = getenv(self::ENV_TMNCODE) ?: $_ENV[self::ENV_TMNCODE];
        $vnp_HashSecret = getenv(self::ENV_HASH_SECRET) ?: $_ENV[self::ENV_HASH_SECRET];
        $vnp_Version = getenv(self::ENV_VERSION) ?: $_ENV[self::ENV_VERSION];

        $currentLocale = self::getCurrentLocale();
        $vnp_Locale = $currentLocale == 'vi' ? 'vn' : 'en';

        $createDate = date(self::DATE_FORMAT);
        $expireDate = date(self::DATE_FORMAT, strtotime("+$expireMinutes minutes",strtotime($createDate)));

        $inputData = array_merge($params, [
            "vnp_Version" => $vnp_Version, // 2.1.0
            "vnp_Command" => self::COMMAND,
            "vnp_CurrCode" => self::CURRENCY_CODE,
            "vnp_CreateDate" => $createDate,
            "vnp_ExpireDate" => $expireDate,
            "vnp_Locale" => $vnp_Locale,
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_OrderType" => self::ORDER_TYPE,
            "vnp_TxnRef" => $txtRef,
            "vnp_Amount" => $amount * self::TRANS_AMOUNT_MULTIPLIER,
            "vnp_OrderInfo" => $orderInfo,
            "vnp_IpAddr" => self::getIP(),
            "vnp_ReturnUrl" => $redirectUrl,
            // "vnp_BankCode" => 'VNPAYQR',
        ]);

        ksort($inputData);

        $query = "";
        $i = 0;
        $hashData = "";

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnp_SecureHash = hash_hmac(self::HASH_CODE, $hashData, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnp_SecureHash;
        }

        return $vnp_Url;
    }

    public static function processTransaction($requestData)
    {
        $inputData = array();
        $order = null;
        $returnData = array(); // data return while callback

        try {
            $vnp_HashSecret = getenv(self::ENV_HASH_SECRET) ?: $_ENV[self::ENV_HASH_SECRET];

            foreach ($requestData as $key => $value) {
                if (substr($key, 0, 4) == "vnp_") {
                    $inputData[$key] = $value;
                }
            }

            $vnp_SecureHash = $inputData['vnp_SecureHash'];
            unset($inputData['vnp_SecureHash']);
            ksort($inputData);

            $i = 0;
            $hashData = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }

            $secureHash = hash_hmac(self::HASH_CODE, $hashData, $vnp_HashSecret);

            try {
                // valid checksum
                if ($secureHash == $vnp_SecureHash) {
                    // find order from transation data
                    $findOrderEvent = new VNPayEvent($inputData);
                    \Pimcore::getEventDispatcher()->dispatch($findOrderEvent, VNPayEvents::FIND_ORDER);
                    $order = $findOrderEvent->getOrder();

                    if ($order) {
                        // check amount
                        if ($findOrderEvent->getAmount() == ($inputData['vnp_Amount'] / self::TRANS_AMOUNT_MULTIPLIER)) {
                            if ($findOrderEvent->getValidStatus()) {
                                $paymentEvent = new VNPayEvent($inputData, $order);

                                if ($inputData['vnp_ResponseCode'] == self::TRANS_SUCCESS_CODE && $inputData['vnp_TransactionStatus'] == self::TRANS_SUCCESS_CODE) {
                                    // success
                                    \Pimcore::getEventDispatcher()->dispatch($paymentEvent, VNPayEvents::PAYMENT_SUCCESS);
                                } else if ($inputData['vnp_ResponseCode'] == self::TRANS_CANCEL_CODE) {
                                    // cancel
                                    \Pimcore::getEventDispatcher()->dispatch($paymentEvent, VNPayEvents::PAYMENT_CANCEL);
                                } else {
                                    // failure
                                    \Pimcore::getEventDispatcher()->dispatch($paymentEvent, VNPayEvents::PAYMENT_FAILURE);
                                }

                                $returnData['RspCode'] = '00';
                                $returnData['Message'] = 'Confirm Success';
                            } else {
                                $returnData['RspCode'] = '02';
                                $returnData['Message'] = 'Order already confirmed';
                            }
                        } else {
                            $returnData['RspCode'] = '04';
                            $returnData['Message'] = 'Invalid amount';
                        }
                    } else {
                        $returnData['RspCode'] = '01';
                        $returnData['Message'] = 'Order not found';
                    }
                } else {
                    $returnData['RspCode'] = '97';
                    $returnData['Message'] = 'Invalid signature';
                }
            } catch (Exception $e) {
                $returnData['RspCode'] = '99';
                $returnData['Message'] = 'Unknow error';
            }
        } catch (\Throwable $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknow error';
        }

        // response
        $transactionEvent = new VNPayEvent($inputData, $order, $returnData);
        \Pimcore::getEventDispatcher()->dispatch($transactionEvent, VNPayEvents::TRANSACTION_RESPONSE);

        return $returnData;
    }
}
