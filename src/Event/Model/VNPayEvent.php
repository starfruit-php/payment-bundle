<?php

namespace Starfruit\PaymentBundle\Event\Model;

use Pimcore\Event\Traits\ArgumentsAwareTrait;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Contracts\EventDispatcher\Event;
use Pimcore\Event\Model\ElementEventInterface;
use Symfony\Component\HttpFoundation\Request;

class VNPayEvent extends Event implements ElementEventInterface
{
    use ArgumentsAwareTrait;

    /**
     * Transaction data.
     * 
     * @var array
     */
    protected $transactionData;

    /**
     * Order object.
     * 
     * @var mixed
     */
    protected $order;

    /**
     * Order total price.
     * 
     * @var int
     */
    protected $amount;

    /**
     * Check if order status is valid.
     * 
     * @var bool
     */
    protected $validStatus;

    /**
     * Return data for callback.
     * 
     * @var array
     */
    protected $returnData;

    public function __construct($transactionData = [], $order = null, $returnData = [])
    {
        $this->transactionData = $transactionData;
        $this->order = $order;
        $this->amount = 0;
        $this->validStatus = false;
        $this->returnData = $returnData;
    }

    public function getTransactionData()
    {
        return $this->transactionData;
    }

    public function getReturnData()
    {
        return $this->returnData;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount(int $amount)
    {
        $this->amount = $amount;
    }

    public function setValidStatus(bool $validStatus)
    {
        $this->validStatus = $validStatus;
    }

    public function getValidStatus()
    {
        return $this->validStatus;
    }

    /**
     * @return AbstractObject
     */
    public function getElement(): \Pimcore\Model\Element\ElementInterface
    {
        return null;
    }
}
