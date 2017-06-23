<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Carrier
 *
 * @ORM\Table(indexes={@ORM\Index(name="deleted", columns={"deleted", "active"}), @ORM\Index(name="id_tax_rules_group", columns={"id_tax_rules_group"}), @ORM\Index(name="reference", columns={"id_reference", "deleted", "active"})})
 * @ORM\Entity
 */
class Carrier
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_reference", type="integer", nullable=false)
     */
    private $idReference;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tax_rules_group", type="integer", nullable=true)
     */
    private $idTaxRulesGroup = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     */
    private $deleted = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="shipping_handling", type="boolean", nullable=false)
     */
    private $shippingHandling = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="range_behavior", type="boolean", nullable=false)
     */
    private $rangeBehavior = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_module", type="boolean", nullable=false)
     */
    private $isModule = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_free", type="boolean", nullable=false)
     */
    private $isFree = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="shipping_external", type="boolean", nullable=false)
     */
    private $shippingExternal = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="need_range", type="boolean", nullable=false)
     */
    private $needRange = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="external_module_name", type="string", length=64, nullable=true)
     */
    private $externalModuleName;

    /**
     * @var integer
     *
     * @ORM\Column(name="shipping_method", type="integer", nullable=false)
     */
    private $shippingMethod = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="max_width", type="integer", nullable=true)
     */
    private $maxWidth = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="max_height", type="integer", nullable=true)
     */
    private $maxHeight = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="max_depth", type="integer", nullable=true)
     */
    private $maxDepth = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="max_weight", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $maxWeight = '0.000000';

    /**
     * @var integer
     *
     * @ORM\Column(name="grade", type="integer", nullable=true)
     */
    private $grade = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_carrier", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCarrier;



    /**
     * Set idReference
     *
     * @param integer $idReference
     *
     * @return Carrier
     */
    public function setIdReference($idReference)
    {
        $this->idReference = $idReference;

        return $this;
    }

    /**
     * Get idReference
     *
     * @return integer
     */
    public function getIdReference()
    {
        return $this->idReference;
    }

    /**
     * Set idTaxRulesGroup
     *
     * @param integer $idTaxRulesGroup
     *
     * @return Carrier
     */
    public function setIdTaxRulesGroup($idTaxRulesGroup)
    {
        $this->idTaxRulesGroup = $idTaxRulesGroup;

        return $this;
    }

    /**
     * Get idTaxRulesGroup
     *
     * @return integer
     */
    public function getIdTaxRulesGroup()
    {
        return $this->idTaxRulesGroup;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Carrier
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
     * Set url
     *
     * @param string $url
     *
     * @return Carrier
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Carrier
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Carrier
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set shippingHandling
     *
     * @param boolean $shippingHandling
     *
     * @return Carrier
     */
    public function setShippingHandling($shippingHandling)
    {
        $this->shippingHandling = $shippingHandling;

        return $this;
    }

    /**
     * Get shippingHandling
     *
     * @return boolean
     */
    public function getShippingHandling()
    {
        return $this->shippingHandling;
    }

    /**
     * Set rangeBehavior
     *
     * @param boolean $rangeBehavior
     *
     * @return Carrier
     */
    public function setRangeBehavior($rangeBehavior)
    {
        $this->rangeBehavior = $rangeBehavior;

        return $this;
    }

    /**
     * Get rangeBehavior
     *
     * @return boolean
     */
    public function getRangeBehavior()
    {
        return $this->rangeBehavior;
    }

    /**
     * Set isModule
     *
     * @param boolean $isModule
     *
     * @return Carrier
     */
    public function setIsModule($isModule)
    {
        $this->isModule = $isModule;

        return $this;
    }

    /**
     * Get isModule
     *
     * @return boolean
     */
    public function getIsModule()
    {
        return $this->isModule;
    }

    /**
     * Set isFree
     *
     * @param boolean $isFree
     *
     * @return Carrier
     */
    public function setIsFree($isFree)
    {
        $this->isFree = $isFree;

        return $this;
    }

    /**
     * Get isFree
     *
     * @return boolean
     */
    public function getIsFree()
    {
        return $this->isFree;
    }

    /**
     * Set shippingExternal
     *
     * @param boolean $shippingExternal
     *
     * @return Carrier
     */
    public function setShippingExternal($shippingExternal)
    {
        $this->shippingExternal = $shippingExternal;

        return $this;
    }

    /**
     * Get shippingExternal
     *
     * @return boolean
     */
    public function getShippingExternal()
    {
        return $this->shippingExternal;
    }

    /**
     * Set needRange
     *
     * @param boolean $needRange
     *
     * @return Carrier
     */
    public function setNeedRange($needRange)
    {
        $this->needRange = $needRange;

        return $this;
    }

    /**
     * Get needRange
     *
     * @return boolean
     */
    public function getNeedRange()
    {
        return $this->needRange;
    }

    /**
     * Set externalModuleName
     *
     * @param string $externalModuleName
     *
     * @return Carrier
     */
    public function setExternalModuleName($externalModuleName)
    {
        $this->externalModuleName = $externalModuleName;

        return $this;
    }

    /**
     * Get externalModuleName
     *
     * @return string
     */
    public function getExternalModuleName()
    {
        return $this->externalModuleName;
    }

    /**
     * Set shippingMethod
     *
     * @param integer $shippingMethod
     *
     * @return Carrier
     */
    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;

        return $this;
    }

    /**
     * Get shippingMethod
     *
     * @return integer
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Carrier
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set maxWidth
     *
     * @param integer $maxWidth
     *
     * @return Carrier
     */
    public function setMaxWidth($maxWidth)
    {
        $this->maxWidth = $maxWidth;

        return $this;
    }

    /**
     * Get maxWidth
     *
     * @return integer
     */
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    /**
     * Set maxHeight
     *
     * @param integer $maxHeight
     *
     * @return Carrier
     */
    public function setMaxHeight($maxHeight)
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    /**
     * Get maxHeight
     *
     * @return integer
     */
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    /**
     * Set maxDepth
     *
     * @param integer $maxDepth
     *
     * @return Carrier
     */
    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth;

        return $this;
    }

    /**
     * Get maxDepth
     *
     * @return integer
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    /**
     * Set maxWeight
     *
     * @param string $maxWeight
     *
     * @return Carrier
     */
    public function setMaxWeight($maxWeight)
    {
        $this->maxWeight = $maxWeight;

        return $this;
    }

    /**
     * Get maxWeight
     *
     * @return string
     */
    public function getMaxWeight()
    {
        return $this->maxWeight;
    }

    /**
     * Set grade
     *
     * @param integer $grade
     *
     * @return Carrier
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade
     *
     * @return integer
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Get idCarrier
     *
     * @return integer
     */
    public function getIdCarrier()
    {
        return $this->idCarrier;
    }
}
