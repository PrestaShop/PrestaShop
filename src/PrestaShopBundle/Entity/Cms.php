<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cms
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Cms
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_cms_category", type="integer", nullable=false)
     */
    private $idCmsCategory;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="indexation", type="boolean", nullable=false)
     */
    private $indexation = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cms", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCms;



    /**
     * Set idCmsCategory
     *
     * @param integer $idCmsCategory
     *
     * @return Cms
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
     * Set position
     *
     * @param integer $position
     *
     * @return Cms
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
     * Set active
     *
     * @param boolean $active
     *
     * @return Cms
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
     * Set indexation
     *
     * @param boolean $indexation
     *
     * @return Cms
     */
    public function setIndexation($indexation)
    {
        $this->indexation = $indexation;

        return $this;
    }

    /**
     * Get indexation
     *
     * @return boolean
     */
    public function getIndexation()
    {
        return $this->indexation;
    }

    /**
     * Get idCms
     *
     * @return integer
     */
    public function getIdCms()
    {
        return $this->idCms;
    }
}
