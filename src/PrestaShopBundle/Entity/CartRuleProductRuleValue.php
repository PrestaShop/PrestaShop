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
 * CartRuleProductRuleValue
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CartRuleProductRuleValue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_rule", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProductRule;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_item", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idItem;



    /**
     * Set idProductRule
     *
     * @param integer $idProductRule
     *
     * @return CartRuleProductRuleValue
     */
    public function setIdProductRule($idProductRule)
    {
        $this->idProductRule = $idProductRule;

        return $this;
    }

    /**
     * Get idProductRule
     *
     * @return integer
     */
    public function getIdProductRule()
    {
        return $this->idProductRule;
    }

    /**
     * Set idItem
     *
     * @param integer $idItem
     *
     * @return CartRuleProductRuleValue
     */
    public function setIdItem($idItem)
    {
        $this->idItem = $idItem;

        return $this;
    }

    /**
     * Get idItem
     *
     * @return integer
     */
    public function getIdItem()
    {
        return $this->idItem;
    }
}
