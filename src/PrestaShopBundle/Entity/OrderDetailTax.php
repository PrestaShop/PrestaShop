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
 * OrderDetailTax
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_order_detail", columns={"id_order_detail"}), @ORM\Index(name="id_tax", columns={"id_tax"})})
 * @ORM\Entity
 */
class OrderDetailTax
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_tax", type="integer", nullable=false)
     */
    private $idTax;

    /**
     * @var string
     *
     * @ORM\Column(name="unit_amount", type="decimal", precision=16, scale=6, nullable=false)
     */
    private $unitAmount = '0.000000';

    /**
     * @var string
     *
     * @ORM\Column(name="total_amount", type="decimal", precision=16, scale=6, nullable=false)
     */
    private $totalAmount = '0.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_detail", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOrderDetail;



    /**
     * Set idTax
     *
     * @param integer $idTax
     *
     * @return OrderDetailTax
     */
    public function setIdTax($idTax)
    {
        $this->idTax = $idTax;

        return $this;
    }

    /**
     * Get idTax
     *
     * @return integer
     */
    public function getIdTax()
    {
        return $this->idTax;
    }

    /**
     * Set unitAmount
     *
     * @param string $unitAmount
     *
     * @return OrderDetailTax
     */
    public function setUnitAmount($unitAmount)
    {
        $this->unitAmount = $unitAmount;

        return $this;
    }

    /**
     * Get unitAmount
     *
     * @return string
     */
    public function getUnitAmount()
    {
        return $this->unitAmount;
    }

    /**
     * Set totalAmount
     *
     * @param string $totalAmount
     *
     * @return OrderDetailTax
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * Get totalAmount
     *
     * @return string
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * Get idOrderDetail
     *
     * @return integer
     */
    public function getIdOrderDetail()
    {
        return $this->idOrderDetail;
    }
}
