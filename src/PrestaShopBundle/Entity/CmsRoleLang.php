<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CmsRoleLang
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CmsRoleLang
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128, nullable=true)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cms_role", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCmsRole;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idLang;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return CmsRoleLang
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
     * Set idCmsRole
     *
     * @param integer $idCmsRole
     *
     * @return CmsRoleLang
     */
    public function setIdCmsRole($idCmsRole)
    {
        $this->idCmsRole = $idCmsRole;

        return $this;
    }

    /**
     * Get idCmsRole
     *
     * @return integer
     */
    public function getIdCmsRole()
    {
        return $this->idCmsRole;
    }

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return CmsRoleLang
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

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return CmsRoleLang
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
}
