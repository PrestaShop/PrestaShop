<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConfigurationKpi
 *
 * @ORM\Table(indexes={@ORM\Index(name="name", columns={"name"}), @ORM\Index(name="id_shop", columns={"id_shop"}), @ORM\Index(name="id_shop_group", columns={"id_shop_group"})})
 * @ORM\Entity
 */
class ConfigurationKpi
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop_group", type="integer", nullable=true)
     */
    private $idShopGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer", nullable=true)
     */
    private $idShop;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", length=65535, nullable=true)
     */
    private $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_upd", type="datetime", nullable=false)
     */
    private $dateUpd;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_configuration_kpi", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idConfigurationKpi;



    /**
     * Set idShopGroup
     *
     * @param integer $idShopGroup
     *
     * @return ConfigurationKpi
     */
    public function setIdShopGroup($idShopGroup)
    {
        $this->idShopGroup = $idShopGroup;

        return $this;
    }

    /**
     * Get idShopGroup
     *
     * @return integer
     */
    public function getIdShopGroup()
    {
        return $this->idShopGroup;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return ConfigurationKpi
     */
    public function setIdShop($idShop)
    {
        $this->idShop = $idShop;

        return $this;
    }

    /**
     * Get idShop
     *
     * @return integer
     */
    public function getIdShop()
    {
        return $this->idShop;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ConfigurationKpi
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
     * Set value
     *
     * @param string $value
     *
     * @return ConfigurationKpi
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
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return ConfigurationKpi
     */
    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * Get dateAdd
     *
     * @return \DateTime
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * Set dateUpd
     *
     * @param \DateTime $dateUpd
     *
     * @return ConfigurationKpi
     */
    public function setDateUpd($dateUpd)
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }

    /**
     * Get dateUpd
     *
     * @return \DateTime
     */
    public function getDateUpd()
    {
        return $this->dateUpd;
    }

    /**
     * Get idConfigurationKpi
     *
     * @return integer
     */
    public function getIdConfigurationKpi()
    {
        return $this->idConfigurationKpi;
    }
}
