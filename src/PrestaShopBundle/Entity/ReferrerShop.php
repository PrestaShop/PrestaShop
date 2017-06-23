<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReferrerShop
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ReferrerShop
{
    /**
     * @var integer
     *
     * @ORM\Column(name="cache_visitors", type="integer", nullable=true)
     */
    private $cacheVisitors;

    /**
     * @var integer
     *
     * @ORM\Column(name="cache_visits", type="integer", nullable=true)
     */
    private $cacheVisits;

    /**
     * @var integer
     *
     * @ORM\Column(name="cache_pages", type="integer", nullable=true)
     */
    private $cachePages;

    /**
     * @var integer
     *
     * @ORM\Column(name="cache_registrations", type="integer", nullable=true)
     */
    private $cacheRegistrations;

    /**
     * @var integer
     *
     * @ORM\Column(name="cache_orders", type="integer", nullable=true)
     */
    private $cacheOrders;

    /**
     * @var string
     *
     * @ORM\Column(name="cache_sales", type="decimal", precision=17, scale=2, nullable=true)
     */
    private $cacheSales;

    /**
     * @var string
     *
     * @ORM\Column(name="cache_reg_rate", type="decimal", precision=5, scale=4, nullable=true)
     */
    private $cacheRegRate;

    /**
     * @var string
     *
     * @ORM\Column(name="cache_order_rate", type="decimal", precision=5, scale=4, nullable=true)
     */
    private $cacheOrderRate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_referrer", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idReferrer;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set cacheVisitors
     *
     * @param integer $cacheVisitors
     *
     * @return ReferrerShop
     */
    public function setCacheVisitors($cacheVisitors)
    {
        $this->cacheVisitors = $cacheVisitors;

        return $this;
    }

    /**
     * Get cacheVisitors
     *
     * @return integer
     */
    public function getCacheVisitors()
    {
        return $this->cacheVisitors;
    }

    /**
     * Set cacheVisits
     *
     * @param integer $cacheVisits
     *
     * @return ReferrerShop
     */
    public function setCacheVisits($cacheVisits)
    {
        $this->cacheVisits = $cacheVisits;

        return $this;
    }

    /**
     * Get cacheVisits
     *
     * @return integer
     */
    public function getCacheVisits()
    {
        return $this->cacheVisits;
    }

    /**
     * Set cachePages
     *
     * @param integer $cachePages
     *
     * @return ReferrerShop
     */
    public function setCachePages($cachePages)
    {
        $this->cachePages = $cachePages;

        return $this;
    }

    /**
     * Get cachePages
     *
     * @return integer
     */
    public function getCachePages()
    {
        return $this->cachePages;
    }

    /**
     * Set cacheRegistrations
     *
     * @param integer $cacheRegistrations
     *
     * @return ReferrerShop
     */
    public function setCacheRegistrations($cacheRegistrations)
    {
        $this->cacheRegistrations = $cacheRegistrations;

        return $this;
    }

    /**
     * Get cacheRegistrations
     *
     * @return integer
     */
    public function getCacheRegistrations()
    {
        return $this->cacheRegistrations;
    }

    /**
     * Set cacheOrders
     *
     * @param integer $cacheOrders
     *
     * @return ReferrerShop
     */
    public function setCacheOrders($cacheOrders)
    {
        $this->cacheOrders = $cacheOrders;

        return $this;
    }

    /**
     * Get cacheOrders
     *
     * @return integer
     */
    public function getCacheOrders()
    {
        return $this->cacheOrders;
    }

    /**
     * Set cacheSales
     *
     * @param string $cacheSales
     *
     * @return ReferrerShop
     */
    public function setCacheSales($cacheSales)
    {
        $this->cacheSales = $cacheSales;

        return $this;
    }

    /**
     * Get cacheSales
     *
     * @return string
     */
    public function getCacheSales()
    {
        return $this->cacheSales;
    }

    /**
     * Set cacheRegRate
     *
     * @param string $cacheRegRate
     *
     * @return ReferrerShop
     */
    public function setCacheRegRate($cacheRegRate)
    {
        $this->cacheRegRate = $cacheRegRate;

        return $this;
    }

    /**
     * Get cacheRegRate
     *
     * @return string
     */
    public function getCacheRegRate()
    {
        return $this->cacheRegRate;
    }

    /**
     * Set cacheOrderRate
     *
     * @param string $cacheOrderRate
     *
     * @return ReferrerShop
     */
    public function setCacheOrderRate($cacheOrderRate)
    {
        $this->cacheOrderRate = $cacheOrderRate;

        return $this;
    }

    /**
     * Get cacheOrderRate
     *
     * @return string
     */
    public function getCacheOrderRate()
    {
        return $this->cacheOrderRate;
    }

    /**
     * Set idReferrer
     *
     * @param integer $idReferrer
     *
     * @return ReferrerShop
     */
    public function setIdReferrer($idReferrer)
    {
        $this->idReferrer = $idReferrer;

        return $this;
    }

    /**
     * Get idReferrer
     *
     * @return integer
     */
    public function getIdReferrer()
    {
        return $this->idReferrer;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return ReferrerShop
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
}
