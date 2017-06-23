<?php

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
