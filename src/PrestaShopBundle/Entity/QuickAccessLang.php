<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuickAccessLang
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class QuickAccessLang
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_quick_access", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idQuickAccess;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idLang;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return QuickAccessLang
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
     * Set idQuickAccess
     *
     * @param integer $idQuickAccess
     *
     * @return QuickAccessLang
     */
    public function setIdQuickAccess($idQuickAccess)
    {
        $this->idQuickAccess = $idQuickAccess;

        return $this;
    }

    /**
     * Get idQuickAccess
     *
     * @return integer
     */
    public function getIdQuickAccess()
    {
        return $this->idQuickAccess;
    }

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return QuickAccessLang
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
