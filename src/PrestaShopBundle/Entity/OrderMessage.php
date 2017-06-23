<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderMessage
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class OrderMessage
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_order_message", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOrderMessage;



    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return OrderMessage
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
     * Get idOrderMessage
     *
     * @return integer
     */
    public function getIdOrderMessage()
    {
        return $this->idOrderMessage;
    }
}
