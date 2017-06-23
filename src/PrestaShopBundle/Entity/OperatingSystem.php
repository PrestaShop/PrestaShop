<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OperatingSystem
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class OperatingSystem
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_operating_system", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idOperatingSystem;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return OperatingSystem
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
     * Get idOperatingSystem
     *
     * @return integer
     */
    public function getIdOperatingSystem()
    {
        return $this->idOperatingSystem;
    }
}
