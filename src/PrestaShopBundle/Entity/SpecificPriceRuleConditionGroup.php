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
 * SpecificPriceRuleConditionGroup
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SpecificPriceRuleConditionGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_specific_price_rule_condition_group", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idSpecificPriceRuleConditionGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_specific_price_rule", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idSpecificPriceRule;



    /**
     * Set idSpecificPriceRuleConditionGroup
     *
     * @param integer $idSpecificPriceRuleConditionGroup
     *
     * @return SpecificPriceRuleConditionGroup
     */
    public function setIdSpecificPriceRuleConditionGroup($idSpecificPriceRuleConditionGroup)
    {
        $this->idSpecificPriceRuleConditionGroup = $idSpecificPriceRuleConditionGroup;

        return $this;
    }

    /**
     * Get idSpecificPriceRuleConditionGroup
     *
     * @return integer
     */
    public function getIdSpecificPriceRuleConditionGroup()
    {
        return $this->idSpecificPriceRuleConditionGroup;
    }

    /**
     * Set idSpecificPriceRule
     *
     * @param integer $idSpecificPriceRule
     *
     * @return SpecificPriceRuleConditionGroup
     */
    public function setIdSpecificPriceRule($idSpecificPriceRule)
    {
        $this->idSpecificPriceRule = $idSpecificPriceRule;

        return $this;
    }

    /**
     * Get idSpecificPriceRule
     *
     * @return integer
     */
    public function getIdSpecificPriceRule()
    {
        return $this->idSpecificPriceRule;
    }
}
