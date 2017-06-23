<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(indexes={@ORM\Index(name="category_parent", columns={"id_parent"}), @ORM\Index(name="nleftrightactive", columns={"nleft", "nright", "active"}), @ORM\Index(name="level_depth", columns={"level_depth"}), @ORM\Index(name="nright", columns={"nright"}), @ORM\Index(name="activenleft", columns={"active", "nleft"}), @ORM\Index(name="activenright", columns={"active", "nright"})})
 * @ORM\Entity
 */
class Category
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_parent", type="integer", nullable=false)
     */
    private $idParent;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop_default", type="integer", nullable=false)
     */
    private $idShopDefault = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="level_depth", type="boolean", nullable=false)
     */
    private $levelDepth = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="nleft", type="integer", nullable=false)
     */
    private $nleft = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="nright", type="integer", nullable=false)
     */
    private $nright = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '0';

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
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_root_category", type="boolean", nullable=false)
     */
    private $isRootCategory = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_category", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCategory;



    /**
     * Set idParent
     *
     * @param integer $idParent
     *
     * @return Category
     */
    public function setIdParent($idParent)
    {
        $this->idParent = $idParent;

        return $this;
    }

    /**
     * Get idParent
     *
     * @return integer
     */
    public function getIdParent()
    {
        return $this->idParent;
    }

    /**
     * Set idShopDefault
     *
     * @param integer $idShopDefault
     *
     * @return Category
     */
    public function setIdShopDefault($idShopDefault)
    {
        $this->idShopDefault = $idShopDefault;

        return $this;
    }

    /**
     * Get idShopDefault
     *
     * @return integer
     */
    public function getIdShopDefault()
    {
        return $this->idShopDefault;
    }

    /**
     * Set levelDepth
     *
     * @param boolean $levelDepth
     *
     * @return Category
     */
    public function setLevelDepth($levelDepth)
    {
        $this->levelDepth = $levelDepth;

        return $this;
    }

    /**
     * Get levelDepth
     *
     * @return boolean
     */
    public function getLevelDepth()
    {
        return $this->levelDepth;
    }

    /**
     * Set nleft
     *
     * @param integer $nleft
     *
     * @return Category
     */
    public function setNleft($nleft)
    {
        $this->nleft = $nleft;

        return $this;
    }

    /**
     * Get nleft
     *
     * @return integer
     */
    public function getNleft()
    {
        return $this->nleft;
    }

    /**
     * Set nright
     *
     * @param integer $nright
     *
     * @return Category
     */
    public function setNright($nright)
    {
        $this->nright = $nright;

        return $this;
    }

    /**
     * Get nright
     *
     * @return integer
     */
    public function getNright()
    {
        return $this->nright;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Category
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
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return Category
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
     * @return Category
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
     * Set position
     *
     * @param integer $position
     *
     * @return Category
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
     * Set isRootCategory
     *
     * @param boolean $isRootCategory
     *
     * @return Category
     */
    public function setIsRootCategory($isRootCategory)
    {
        $this->isRootCategory = $isRootCategory;

        return $this;
    }

    /**
     * Get isRootCategory
     *
     * @return boolean
     */
    public function getIsRootCategory()
    {
        return $this->isRootCategory;
    }

    /**
     * Get idCategory
     *
     * @return integer
     */
    public function getIdCategory()
    {
        return $this->idCategory;
    }
}
