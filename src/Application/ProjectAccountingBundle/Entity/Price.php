<?php


namespace Application\ProjectAccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Embeddable */
class Price
{
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $amount = 0.0;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    private $currency = 'HUF';

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function toArray()
    {
        return array(
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
        );
    }
}