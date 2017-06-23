<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderReturnState
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class OrderReturnState
{
    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=32, nullable=true)
     */
    private $color;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_return_state", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOrderReturnState;



    /**
     * Set color
     *
     * @param string $color
     *
     * @return OrderReturnState
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
     * Get idOrderReturnState
     *
     * @return integer
     */
    public function getIdOrderReturnState()
    {
        return $this->idOrderReturnState;
    }
}
