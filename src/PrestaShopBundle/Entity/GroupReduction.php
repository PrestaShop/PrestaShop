<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GroupReduction
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_group", columns={"id_group", "id_category"})})
 * @ORM\Entity
 */
class GroupReduction
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_group", type="integer", nullable=false)
     */
    private $idGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_category", type="integer", nullable=false)
     */
    private $idCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="reduction", type="decimal", precision=4, scale=3, nullable=false)
     */
    private $reduction;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_group_reduction", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idGroupReduction;



    /**
     * Set idGroup
     *
     * @param integer $idGroup
     *
     * @return GroupReduction
     */
    public function setIdGroup($idGroup)
    {
        $this->idGroup = $idGroup;

        return $this;
    }

    /**
     * Get idGroup
     *
     * @return integer
     */
    public function getIdGroup()
    {
        return $this->idGroup;
    }

    /**
     * Set idCategory
     *
     * @param integer $idCategory
     *
     * @return GroupReduction
     */
    public function setIdCategory($idCategory)
    {
        $this->idCategory = $idCategory;

        return $this;
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

    /**
     * Set reduction
     *
     * @param string $reduction
     *
     * @return GroupReduction
     */
    public function setReduction($reduction)
    {
        $this->reduction = $reduction;

        return $this;
    }

    /**
     * Get reduction
     *
     * @return string
     */
    public function getReduction()
    {
        return $this->reduction;
    }

    /**
     * Get idGroupReduction
     *
     * @return integer
     */
    public function getIdGroupReduction()
    {
        return $this->idGroupReduction;
    }
}
