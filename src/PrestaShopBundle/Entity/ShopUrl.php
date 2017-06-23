<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShopUrl
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="full_shop_url", columns={"domain", "physical_uri", "virtual_uri"}), @ORM\UniqueConstraint(name="full_shop_url_ssl", columns={"domain_ssl", "physical_uri", "virtual_uri"})}, indexes={@ORM\Index(name="id_shop", columns={"id_shop", "main"})})
 * @ORM\Entity
 */
class ShopUrl
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer", nullable=false)
     */
    private $idShop;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=150, nullable=false)
     */
    private $domain;

    /**
     * @var string
     *
     * @ORM\Column(name="domain_ssl", type="string", length=150, nullable=false)
     */
    private $domainSsl;

    /**
     * @var string
     *
     * @ORM\Column(name="physical_uri", type="string", length=64, nullable=false)
     */
    private $physicalUri;

    /**
     * @var string
     *
     * @ORM\Column(name="virtual_uri", type="string", length=64, nullable=false)
     */
    private $virtualUri;

    /**
     * @var boolean
     *
     * @ORM\Column(name="main", type="boolean", nullable=false)
     */
    private $main;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop_url", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idShopUrl;



    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return ShopUrl
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
     * Set domain
     *
     * @param string $domain
     *
     * @return ShopUrl
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set domainSsl
     *
     * @param string $domainSsl
     *
     * @return ShopUrl
     */
    public function setDomainSsl($domainSsl)
    {
        $this->domainSsl = $domainSsl;

        return $this;
    }

    /**
     * Get domainSsl
     *
     * @return string
     */
    public function getDomainSsl()
    {
        return $this->domainSsl;
    }

    /**
     * Set physicalUri
     *
     * @param string $physicalUri
     *
     * @return ShopUrl
     */
    public function setPhysicalUri($physicalUri)
    {
        $this->physicalUri = $physicalUri;

        return $this;
    }

    /**
     * Get physicalUri
     *
     * @return string
     */
    public function getPhysicalUri()
    {
        return $this->physicalUri;
    }

    /**
     * Set virtualUri
     *
     * @param string $virtualUri
     *
     * @return ShopUrl
     */
    public function setVirtualUri($virtualUri)
    {
        $this->virtualUri = $virtualUri;

        return $this;
    }

    /**
     * Get virtualUri
     *
     * @return string
     */
    public function getVirtualUri()
    {
        return $this->virtualUri;
    }

    /**
     * Set main
     *
     * @param boolean $main
     *
     * @return ShopUrl
     */
    public function setMain($main)
    {
        $this->main = $main;

        return $this;
    }

    /**
     * Get main
     *
     * @return boolean
     */
    public function getMain()
    {
        return $this->main;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return ShopUrl
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
     * Get idShopUrl
     *
     * @return integer
     */
    public function getIdShopUrl()
    {
        return $this->idShopUrl;
    }
}
