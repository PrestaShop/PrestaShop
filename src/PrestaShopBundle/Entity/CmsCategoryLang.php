<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CmsCategoryLang
 *
 * @ORM\Table(indexes={@ORM\Index(name="category_name", columns={"name"})})
 * @ORM\Entity
 */
class CmsCategoryLang
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="link_rewrite", type="string", length=128, nullable=false)
     */
    private $linkRewrite;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_title", type="string", length=128, nullable=true)
     */
    private $metaTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_keywords", type="string", length=255, nullable=true)
     */
    private $metaKeywords;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="string", length=255, nullable=true)
     */
    private $metaDescription;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cms_category", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCmsCategory;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;

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
     * @return CmsCategoryLang
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
     * Set description
     *
     * @param string $description
     *
     * @return CmsCategoryLang
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
     * Set linkRewrite
     *
     * @param string $linkRewrite
     *
     * @return CmsCategoryLang
     */
    public function setLinkRewrite($linkRewrite)
    {
        $this->linkRewrite = $linkRewrite;

        return $this;
    }

    /**
     * Get linkRewrite
     *
     * @return string
     */
    public function getLinkRewrite()
    {
        return $this->linkRewrite;
    }

    /**
     * Set metaTitle
     *
     * @param string $metaTitle
     *
     * @return CmsCategoryLang
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * Get metaTitle
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Set metaKeywords
     *
     * @param string $metaKeywords
     *
     * @return CmsCategoryLang
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * Get metaKeywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     *
     * @return CmsCategoryLang
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set idCmsCategory
     *
     * @param integer $idCmsCategory
     *
     * @return CmsCategoryLang
     */
    public function setIdCmsCategory($idCmsCategory)
    {
        $this->idCmsCategory = $idCmsCategory;

        return $this;
    }

    /**
     * Get idCmsCategory
     *
     * @return integer
     */
    public function getIdCmsCategory()
    {
        return $this->idCmsCategory;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return CmsCategoryLang
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
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return CmsCategoryLang
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
