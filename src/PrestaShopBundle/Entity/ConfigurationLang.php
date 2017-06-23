<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConfigurationLang
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ConfigurationLang
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
     * @ORM\Column(name="id_configuration", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idConfiguration;

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
     * @return ConfigurationLang
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
     * @return ConfigurationLang
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
     * Set idConfiguration
     *
     * @param integer $idConfiguration
     *
     * @return ConfigurationLang
     */
    public function setIdConfiguration($idConfiguration)
    {
        $this->idConfiguration = $idConfiguration;

        return $this;
    }

    /**
     * Get idConfiguration
     *
     * @return integer
     */
    public function getIdConfiguration()
    {
        return $this->idConfiguration;
    }

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return ConfigurationLang
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
