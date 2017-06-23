<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_page_type", columns={"id_page_type"}), @ORM\Index(name="id_object", columns={"id_object"})})
 * @ORM\Entity
 */
class Page
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_page_type", type="integer", nullable=false)
     */
    private $idPageType;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_object", type="integer", nullable=true)
     */
    private $idObject;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_page", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPage;



    /**
     * Set idPageType
     *
     * @param integer $idPageType
     *
     * @return Page
     */
    public function setIdPageType($idPageType)
    {
        $this->idPageType = $idPageType;

        return $this;
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

    /**
     * Set idObject
     *
     * @param integer $idObject
     *
     * @return Page
     */
    public function setIdObject($idObject)
    {
        $this->idObject = $idObject;

        return $this;
    }

    /**
     * Get idObject
     *
     * @return integer
     */
    public function getIdObject()
    {
        return $this->idObject;
    }

    /**
     * Get idPage
     *
     * @return integer
     */
    public function getIdPage()
    {
        return $this->idPage;
    }
}
