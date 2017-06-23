<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaxRule
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_tax_rules_group", columns={"id_tax_rules_group"}), @ORM\Index(name="id_tax", columns={"id_tax"}), @ORM\Index(name="category_getproducts", columns={"id_tax_rules_group", "id_country", "id_state", "zipcode_from"})})
 * @ORM\Entity
 */
class TaxRule
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_tax_rules_group", type="integer", nullable=false)
     */
    private $idTaxRulesGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_country", type="integer", nullable=false)
     */
    private $idCountry;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_state", type="integer", nullable=false)
     */
    private $idState;

    /**
     * @var string
     *
     * @ORM\Column(name="zipcode_from", type="string", length=12, nullable=false)
     */
    private $zipcodeFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="zipcode_to", type="string", length=12, nullable=false)
     */
    private $zipcodeTo;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tax", type="integer", nullable=false)
     */
    private $idTax;

    /**
     * @var integer
     *
     * @ORM\Column(name="behavior", type="integer", nullable=false)
     */
    private $behavior;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_tax_rule", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idTaxRule;



    /**
     * Set idTaxRulesGroup
     *
     * @param integer $idTaxRulesGroup
     *
     * @return TaxRule
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
     * Set idCountry
     *
     * @param integer $idCountry
     *
     * @return TaxRule
     */
    public function setIdCountry($idCountry)
    {
        $this->idCountry = $idCountry;

        return $this;
    }

    /**
     * Get idCountry
     *
     * @return integer
     */
    public function getIdCountry()
    {
        return $this->idCountry;
    }

    /**
     * Set idState
     *
     * @param integer $idState
     *
     * @return TaxRule
     */
    public function setIdState($idState)
    {
        $this->idState = $idState;

        return $this;
    }

    /**
     * Get idState
     *
     * @return integer
     */
    public function getIdState()
    {
        return $this->idState;
    }

    /**
     * Set zipcodeFrom
     *
     * @param string $zipcodeFrom
     *
     * @return TaxRule
     */
    public function setZipcodeFrom($zipcodeFrom)
    {
        $this->zipcodeFrom = $zipcodeFrom;

        return $this;
    }

    /**
     * Get zipcodeFrom
     *
     * @return string
     */
    public function getZipcodeFrom()
    {
        return $this->zipcodeFrom;
    }

    /**
     * Set zipcodeTo
     *
     * @param string $zipcodeTo
     *
     * @return TaxRule
     */
    public function setZipcodeTo($zipcodeTo)
    {
        $this->zipcodeTo = $zipcodeTo;

        return $this;
    }

    /**
     * Get zipcodeTo
     *
     * @return string
     */
    public function getZipcodeTo()
    {
        return $this->zipcodeTo;
    }

    /**
     * Set idTax
     *
     * @param integer $idTax
     *
     * @return TaxRule
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
     * Set behavior
     *
     * @param integer $behavior
     *
     * @return TaxRule
     */
    public function setBehavior($behavior)
    {
        $this->behavior = $behavior;

        return $this;
    }

    /**
     * Get behavior
     *
     * @return integer
     */
    public function getBehavior()
    {
        return $this->behavior;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return TaxRule
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get idTaxRule
     *
     * @return integer
     */
    public function getIdTaxRule()
    {
        return $this->idTaxRule;
    }
}
