<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * State
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_country", columns={"id_country"}), @ORM\Index(name="name", columns={"name"}), @ORM\Index(name="id_zone", columns={"id_zone"})})
 * @ORM\Entity
 */
class State
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_country", type="integer", nullable=false)
     */
    private $idCountry;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_zone", type="integer", nullable=false)
     */
    private $idZone;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="iso_code", type="string", length=7, nullable=false)
     */
    private $isoCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="tax_behavior", type="smallint", nullable=false)
     */
    private $taxBehavior = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_state", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idState;



    /**
     * Set idCountry
     *
     * @param integer $idCountry
     *
     * @return State
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
     * Set idZone
     *
     * @param integer $idZone
     *
     * @return State
     */
    public function setIdZone($idZone)
    {
        $this->idZone = $idZone;

        return $this;
    }

    /**
     * Get idZone
     *
     * @return integer
     */
    public function getIdZone()
    {
        return $this->idZone;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return State
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
     * Set isoCode
     *
     * @param string $isoCode
     *
     * @return State
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * Get isoCode
     *
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Set taxBehavior
     *
     * @param integer $taxBehavior
     *
     * @return State
     */
    public function setTaxBehavior($taxBehavior)
    {
        $this->taxBehavior = $taxBehavior;

        return $this;
    }

    /**
     * Get taxBehavior
     *
     * @return integer
     */
    public function getTaxBehavior()
    {
        return $this->taxBehavior;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return State
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
     * Get idState
     *
     * @return integer
     */
    public function getIdState()
    {
        return $this->idState;
    }
}
