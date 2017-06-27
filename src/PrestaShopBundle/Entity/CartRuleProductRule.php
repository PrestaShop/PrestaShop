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
 * CartRuleProductRule
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CartRuleProductRule
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_rule_group", type="integer", nullable=false)
     */
    private $idProductRuleGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false, columnDefinition="ENUM('products','categories','attributes','manufacturers','suppliers')")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_rule", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idProductRule;



    /**
     * Set idProductRuleGroup
     *
     * @param integer $idProductRuleGroup
     *
     * @return CartRuleProductRule
     */
    public function setIdProductRuleGroup($idProductRuleGroup)
    {
        $this->idProductRuleGroup = $idProductRuleGroup;

        return $this;
    }

    /**
     * Get idProductRuleGroup
     *
     * @return integer
     */
    public function getIdProductRuleGroup()
    {
        return $this->idProductRuleGroup;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return CartRuleProductRule
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
}
