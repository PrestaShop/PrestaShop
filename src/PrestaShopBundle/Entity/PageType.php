<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageType
 *
 * @ORM\Table(indexes={@ORM\Index(name="name", columns={"name"})})
 * @ORM\Entity
 */
class PageType
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_page_type", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPageType;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return PageType
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
     * Get idPageType
     *
     * @return integer
     */
    public function getIdPageType()
    {
        return $this->idPageType;
    }
}
