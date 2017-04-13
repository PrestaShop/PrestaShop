<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Api\Stock;

use PrestaShopBundle\Entity\ProductIdentity;
use PrestaShop\PrestaShop\Adapter\Configuration;

class Movement
{
    /**
     * @var ProductIdentity
     */
    private $productIdentity;

    /**
     * @var int
     */
    private $delta;

    /**
     * @var int
     */
    private $idStock = 0;

    /**
     * @var int
     */
    private $idOrder = 0;

    /**
     * @var int
     */
    private $idSupplyOrder = 0;

    /**
     * @var int
     */
    private $idStockMvtReason = 0;


    public function __construct(ProductIdentity $productIdentity, $delta)
    {
        $this->productIdentity = $productIdentity;
        $this->delta = (int)$delta;
    }

    /**
     * @return ProductIdentity
     */
    public function getProductIdentity()
    {
        return $this->productIdentity;
    }

    /**
     * @return int
     */
    public function getDelta()
    {
        return $this->delta;
    }

    /**
     * Set idStock
     * @param $idStock
     */
    public function setIdStock($idStock)
    {
        $this->idStock = (int)$idStock;
    }

    /**
     * @return int
     */
    public function getIdStock()
    {
        return $this->idStock;
    }

    /**
     * Set idOrder
     * @param $idOrder
     */
    public function setIdOrder($idOrder)
    {
        $this->idOrder = (int)$idOrder;
    }

    /**
     * @return int
     */
    public function getIdOrder()
    {
        return $this->idOrder;
    }

    /**
     * Set idSupplyOrder
     * @param $idSupplyOrder
     */
    public function setIdSupplyOrder($idSupplyOrder)
    {
        $this->idSupplyOrder = (int)$idSupplyOrder;
    }

    /**
     * @return int
     */
    public function getIdSupplyOrder()
    {
        return $this->idSupplyOrder;
    }

    /**
     * Set idStockMvtReason
     * @param $idStockMvtReason
     */
    public function setIdStockMvtReason($idStockMvtReason)
    {
        $this->idStockMvtReason = (int)$idStockMvtReason;
    }

    /**
     * @return int
     */
    public function getIdStockMvtReason()
    {
        if (0 === $this->idStockMvtReason) {
            $configuration = new Configuration;
            $this->setIdStockMvtReason($this->delta >= 1 ? $configuration->get('PS_STOCK_MVT_INC_REASON_DEFAULT') : $configuration->get('PS_STOCK_MVT_DEC_REASON_DEFAULT'));
        }

        return $this->idStockMvtReason;
    }
}
