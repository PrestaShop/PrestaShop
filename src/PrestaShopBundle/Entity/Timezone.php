<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Timezone
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Timezone
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
     * @ORM\Column(name="id_timezone", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idTimezone;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return Timezone
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
     * Get idTimezone
     *
     * @return integer
     */
    public function getIdTimezone()
    {
        return $this->idTimezone;
    }
}
