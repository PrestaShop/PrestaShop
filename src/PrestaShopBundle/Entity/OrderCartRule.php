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
 * OrderCartRule
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_order", columns={"id_order"}), @ORM\Index(name="id_cart_rule", columns={"id_cart_rule"})})
 * @ORM\Entity
 */
class OrderCartRule
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_order", type="integer", nullable=false)
     */
    private $idOrder;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cart_rule", type="integer", nullable=false)
     */
    private $idCartRule;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_invoice", type="integer", nullable=true, options={"default":0})
     */
    private $idOrderInvoice = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=254, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="decimal", precision=17, scale=2, nullable=false, options={"default":0.00})
     */
    private $value = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="value_tax_excl", type="decimal", precision=17, scale=2, nullable=false, options={"default":0.00})
     */
    private $valueTaxExcl = '0.00';

    /**
     * @var boolean
     *
     * @ORM\Column(name="free_shipping", type="boolean", nullable=false, options={"default":0})
     */
    private $freeShipping = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_cart_rule", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOrderCartRule;



    /**
     * Set idOrder
     *
     * @param integer $idOrder
     *
     * @return OrderCartRule
     */
    public function setIdOrder($idOrder)
    {
        $this->idOrder = $idOrder;

        return $this;
    }

    /**
     * Get idOrder
     *
     * @return integer
     */
    public function getIdOrder()
    {
        return $this->idOrder;
    }

    /**
     * Set idCartRule
     *
     * @param integer $idCartRule
     *
     * @return OrderCartRule
     */
    public function setIdCartRule($idCartRule)
    {
        $this->idCartRule = $idCartRule;

        return $this;
    }

    /**
     * Get idCartRule
     *
     * @return integer
     */
    public function getIdCartRule()
    {
        return $this->idCartRule;
    }

    /**
     * Set idOrderInvoice
     *
     * @param integer $idOrderInvoice
     *
     * @return OrderCartRule
     */
    public function setIdOrderInvoice($idOrderInvoice)
    {
        $this->idOrderInvoice = $idOrderInvoice;

        return $this;
    }

    /**
     * Get idOrderInvoice
     *
     * @return integer
     */
    public function getIdOrderInvoice()
    {
        return $this->idOrderInvoice;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return OrderCartRule
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return OrderCartRule
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set valueTaxExcl
     *
     * @param string $valueTaxExcl
     *
     * @return OrderCartRule
     */
    public function setValueTaxExcl($valueTaxExcl)
    {
        $this->valueTaxExcl = $valueTaxExcl;

        return $this;
    }

    /**
     * Get valueTaxExcl
     *
     * @return string
     */
    public function getValueTaxExcl()
    {
        return $this->valueTaxExcl;
    }

    /**
     * Set freeShipping
     *
     * @param boolean $freeShipping
     *
     * @return OrderCartRule
     */
    public function setFreeShipping($freeShipping)
    {
        $this->freeShipping = $freeShipping;

        return $this;
    }

    /**
     * Get freeShipping
     *
     * @return boolean
     */
    public function getFreeShipping()
    {
        return $this->freeShipping;
    }

    /**
     * Get idOrderCartRule
     *
     * @return integer
     */
    public function getIdOrderCartRule()
    {
        return $this->idOrderCartRule;
    }
}
