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
 * Group
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Group
{
    /**
     * @var string
     *
     * @ORM\Column(name="reduction", type="decimal", precision=17, scale=2, nullable=false, options={"default":0.00})
     */
    private $reduction = '0.00';

    /**
     * @var integer
     *
     * @ORM\Column(name="price_display_method", type="smallint", nullable=false, options={"default":0})
     */
    private $priceDisplayMethod = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_prices", type="boolean", nullable=false, options={"default":1})
     */
    private $showPrices = '1';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_upd", type="datetime", nullable=false)
     */
    private $dateUpd;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_group", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idGroup;



    /**
     * Set reduction
     *
     * @param string $reduction
     *
     * @return Group
     */
    public function setReduction($reduction)
    {
        $this->reduction = $reduction;

        return $this;
    }

    /**
     * Get reduction
     *
     * @return string
     */
    public function getReduction()
    {
        return $this->reduction;
    }

    /**
     * Set priceDisplayMethod
     *
     * @param integer $priceDisplayMethod
     *
     * @return Group
     */
    public function setPriceDisplayMethod($priceDisplayMethod)
    {
        $this->priceDisplayMethod = $priceDisplayMethod;

        return $this;
    }

    /**
     * Get priceDisplayMethod
     *
     * @return integer
     */
    public function getPriceDisplayMethod()
    {
        return $this->priceDisplayMethod;
    }

    /**
     * Set showPrices
     *
     * @param boolean $showPrices
     *
     * @return Group
     */
    public function setShowPrices($showPrices)
    {
        $this->showPrices = $showPrices;

        return $this;
    }

    /**
     * Get showPrices
     *
     * @return boolean
     */
    public function getShowPrices()
    {
        return $this->showPrices;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return Group
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
     * Set dateUpd
     *
     * @param \DateTime $dateUpd
     *
     * @return Group
     */
    public function setDateUpd($dateUpd)
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }

    /**
     * Get dateUpd
     *
     * @return \DateTime
     */
    public function getDateUpd()
    {
        return $this->dateUpd;
    }

    /**
     * Get idGroup
     *
     * @return integer
     */
    public function getIdGroup()
    {
        return $this->idGroup;
    }
}
