<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderPayment
 *
 * @ORM\Table(indexes={@ORM\Index(name="order_reference", columns={"order_reference"})})
 * @ORM\Entity
 */
class OrderPayment
{
    /**
     * @var string
     *
     * @ORM\Column(name="order_reference", type="string", length=9, nullable=true)
     */
    private $orderReference;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_currency", type="integer", nullable=false)
     */
    private $idCurrency;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_method", type="string", length=255, nullable=false)
     */
    private $paymentMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="conversion_rate", type="decimal", precision=13, scale=6, nullable=false, options={"default":1.000000})
     */
    private $conversionRate = '1.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_id", type="string", length=254, nullable=true)
     */
    private $transactionId;

    /**
     * @var string
     *
     * @ORM\Column(name="card_number", type="string", length=254, nullable=true)
     */
    private $cardNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="card_brand", type="string", length=254, nullable=true)
     */
    private $cardBrand;

    /**
     * @var string
     *
     * @ORM\Column(name="card_expiration", type="string", length=7, nullable=true)
     */
    private $cardExpiration;

    /**
     * @var string
     *
     * @ORM\Column(name="card_holder", type="string", length=254, nullable=true)
     */
    private $cardHolder;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_payment", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOrderPayment;



    /**
     * Set orderReference
     *
     * @param string $orderReference
     *
     * @return OrderPayment
     */
    public function setOrderReference($orderReference)
    {
        $this->orderReference = $orderReference;

        return $this;
    }

    /**
     * Get orderReference
     *
     * @return string
     */
    public function getOrderReference()
    {
        return $this->orderReference;
    }

    /**
     * Set idCurrency
     *
     * @param integer $idCurrency
     *
     * @return OrderPayment
     */
    public function setIdCurrency($idCurrency)
    {
        $this->idCurrency = $idCurrency;

        return $this;
    }

    /**
     * Get idCurrency
     *
     * @return integer
     */
    public function getIdCurrency()
    {
        return $this->idCurrency;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return OrderPayment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set paymentMethod
     *
     * @param string $paymentMethod
     *
     * @return OrderPayment
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set conversionRate
     *
     * @param string $conversionRate
     *
     * @return OrderPayment
     */
    public function setConversionRate($conversionRate)
    {
        $this->conversionRate = $conversionRate;

        return $this;
    }

    /**
     * Get conversionRate
     *
     * @return string
     */
    public function getConversionRate()
    {
        return $this->conversionRate;
    }

    /**
     * Set transactionId
     *
     * @param string $transactionId
     *
     * @return OrderPayment
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get transactionId
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Set cardNumber
     *
     * @param string $cardNumber
     *
     * @return OrderPayment
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    /**
     * Get cardNumber
     *
     * @return string
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * Set cardBrand
     *
     * @param string $cardBrand
     *
     * @return OrderPayment
     */
    public function setCardBrand($cardBrand)
    {
        $this->cardBrand = $cardBrand;

        return $this;
    }

    /**
     * Get cardBrand
     *
     * @return string
     */
    public function getCardBrand()
    {
        return $this->cardBrand;
    }

    /**
     * Set cardExpiration
     *
     * @param string $cardExpiration
     *
     * @return OrderPayment
     */
    public function setCardExpiration($cardExpiration)
    {
        $this->cardExpiration = $cardExpiration;

        return $this;
    }

    /**
     * Get cardExpiration
     *
     * @return string
     */
    public function getCardExpiration()
    {
        return $this->cardExpiration;
    }

    /**
     * Set cardHolder
     *
     * @param string $cardHolder
     *
     * @return OrderPayment
     */
    public function setCardHolder($cardHolder)
    {
        $this->cardHolder = $cardHolder;

        return $this;
    }

    /**
     * Get cardHolder
     *
     * @return string
     */
    public function getCardHolder()
    {
        return $this->cardHolder;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return OrderPayment
     */
    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * Get dateAdd
     *
     * @return \DateTime
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * Get idOrderPayment
     *
     * @return integer
     */
    public function getIdOrderPayment()
    {
        return $this->idOrderPayment;
    }
}
