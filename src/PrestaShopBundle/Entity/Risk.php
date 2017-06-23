<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Risk
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Risk
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="percent", type="boolean", nullable=false)
     */
    private $percent;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=32, nullable=true)
     */
    private $color;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_risk", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idRisk;



    /**
     * Set percent
     *
     * @param boolean $percent
     *
     * @return Risk
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Get percent
     *
     * @return boolean
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return Risk
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Get idRisk
     *
     * @return integer
     */
    public function getIdRisk()
    {
        return $this->idRisk;
    }
}
