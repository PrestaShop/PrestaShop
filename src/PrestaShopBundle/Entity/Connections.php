<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Connections
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_guest", columns={"id_guest"}), @ORM\Index(name="date_add", columns={"date_add"}), @ORM\Index(name="id_page", columns={"id_page"})})
 * @ORM\Entity
 */
class Connections
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop_group", type="integer", nullable=false)
     */
    private $idShopGroup = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer", nullable=false)
     */
    private $idShop = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_guest", type="integer", nullable=false)
     */
    private $idGuest;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_page", type="integer", nullable=false)
     */
    private $idPage;

    /**
     * @var integer
     *
     * @ORM\Column(name="ip_address", type="bigint", nullable=true)
     */
    private $ipAddress;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var string
     *
     * @ORM\Column(name="http_referer", type="string", length=255, nullable=true)
     */
    private $httpReferer;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_connections", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idConnections;



    /**
     * Set idShopGroup
     *
     * @param integer $idShopGroup
     *
     * @return Connections
     */
    public function setIdShopGroup($idShopGroup)
    {
        $this->idShopGroup = $idShopGroup;

        return $this;
    }

    /**
     * Get idShopGroup
     *
     * @return integer
     */
    public function getIdShopGroup()
    {
        return $this->idShopGroup;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return Connections
     */
    public function setIdShop($idShop)
    {
        $this->idShop = $idShop;

        return $this;
    }

    /**
     * Get idShop
     *
     * @return integer
     */
    public function getIdShop()
    {
        return $this->idShop;
    }

    /**
     * Set idGuest
     *
     * @param integer $idGuest
     *
     * @return Connections
     */
    public function setIdGuest($idGuest)
    {
        $this->idGuest = $idGuest;

        return $this;
    }

    /**
     * Get idGuest
     *
     * @return integer
     */
    public function getIdGuest()
    {
        return $this->idGuest;
    }

    /**
     * Set idPage
     *
     * @param integer $idPage
     *
     * @return Connections
     */
    public function setIdPage($idPage)
    {
        $this->idPage = $idPage;

        return $this;
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

    /**
     * Set ipAddress
     *
     * @param integer $ipAddress
     *
     * @return Connections
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get ipAddress
     *
     * @return integer
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return Connections
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
     * Set httpReferer
     *
     * @param string $httpReferer
     *
     * @return Connections
     */
    public function setHttpReferer($httpReferer)
    {
        $this->httpReferer = $httpReferer;

        return $this;
    }

    /**
     * Get httpReferer
     *
     * @return string
     */
    public function getHttpReferer()
    {
        return $this->httpReferer;
    }

    /**
     * Get idConnections
     *
     * @return integer
     */
    public function getIdConnections()
    {
        return $this->idConnections;
    }
}
