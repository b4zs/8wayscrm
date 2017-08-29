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

    public function __construct($amount, $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function fromArray(array $data)
    {
        return new Price($data['amount'], $data['currency']);
    }

    public function toArray()
    {
        return array(
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
        );
    }


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

    public function add(Price $price)
    {
        if ($price->getCurrency() !== $this->getCurrency()) {
            throw new \InvalidArgumentException('Currency mismatch');
        }

        $this->setAmount($this->getAmount()+$price->getAmount());
    }
}