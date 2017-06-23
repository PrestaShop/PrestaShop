<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SpecificPriceRuleCondition
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_specific_price_rule_condition_group", columns={"id_specific_price_rule_condition_group"})})
 * @ORM\Entity
 */
class SpecificPriceRuleCondition
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_specific_price_rule_condition_group", type="integer", nullable=false)
     */
    private $idSpecificPriceRuleConditionGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=false)
     */
    private $value;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_specific_price_rule_condition", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSpecificPriceRuleCondition;



    /**
     * Set idSpecificPriceRuleConditionGroup
     *
     * @param integer $idSpecificPriceRuleConditionGroup
     *
     * @return SpecificPriceRuleCondition
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
     * Set type
     *
     * @param string $type
     *
     * @return SpecificPriceRuleCondition
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
     * Set value
     *
     * @param string $value
     *
     * @return SpecificPriceRuleCondition
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
     * Get idSpecificPriceRuleCondition
     *
     * @return integer
     */
    public function getIdSpecificPriceRuleCondition()
    {
        return $this->idSpecificPriceRuleCondition;
    }
}
