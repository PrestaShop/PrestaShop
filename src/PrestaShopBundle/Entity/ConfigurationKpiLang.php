<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConfigurationKpiLang
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ConfigurationKpiLang
{
    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", length=65535, nullable=true)
     */
    private $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_upd", type="datetime", nullable=true)
     */
    private $dateUpd;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_configuration_kpi", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idConfigurationKpi;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idLang;



    /**
     * Set value
     *
     * @param string $value
     *
     * @return ConfigurationKpiLang
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
     * Set dateUpd
     *
     * @param \DateTime $dateUpd
     *
     * @return ConfigurationKpiLang
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
     * Set idConfigurationKpi
     *
     * @param integer $idConfigurationKpi
     *
     * @return ConfigurationKpiLang
     */
    public function setIdConfigurationKpi($idConfigurationKpi)
    {
        $this->idConfigurationKpi = $idConfigurationKpi;

        return $this;
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

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return ConfigurationKpiLang
     */
    public function setIdLang($idLang)
    {
        $this->idLang = $idLang;

        return $this;
    }

    /**
     * Get idLang
     *
     * @return integer
     */
    public function getIdLang()
    {
        return $this->idLang;
    }
}
