<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CmsCategory
 *
 * @ORM\Table(indexes={@ORM\Index(name="category_parent", columns={"id_parent"})})
 * @ORM\Entity
 */
class CmsCategory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_parent", type="integer", nullable=false)
     */
    private $idParent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="level_depth", type="boolean", nullable=false)
     */
    private $levelDepth = '0';

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
     * @var integer
     *
     * @ORM\Column(name="id_cms_category", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCmsCategory;



    /**
     * Set idParent
     *
     * @param integer $idParent
     *
     * @return CmsCategory
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
     * Set levelDepth
     *
     * @param boolean $levelDepth
     *
     * @return CmsCategory
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
     * Set active
     *
     * @param boolean $active
     *
     * @return CmsCategory
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
     * @return CmsCategory
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
     * @return CmsCategory
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
     * @return CmsCategory
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
     * Get idCmsCategory
     *
     * @return integer
     */
    public function getIdCmsCategory()
    {
        return $this->idCmsCategory;
    }
}
