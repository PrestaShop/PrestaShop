<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageViewed
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PageViewed
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
     * @ORM\Column(name="counter", type="integer", nullable=false)
     */
    private $counter;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_page", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idPage;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_date_range", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idDateRange;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idShop;



    /**
     * Set idShopGroup
     *
     * @param integer $idShopGroup
     *
     * @return PageViewed
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
     * Set counter
     *
     * @param integer $counter
     *
     * @return PageViewed
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * Get counter
     *
     * @return integer
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * Set idPage
     *
     * @param integer $idPage
     *
     * @return PageViewed
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
     * Set idDateRange
     *
     * @param integer $idDateRange
     *
     * @return PageViewed
     */
    public function setIdDateRange($idDateRange)
    {
        $this->idDateRange = $idDateRange;

        return $this;
    }

    /**
     * Get idDateRange
     *
     * @return integer
     */
    public function getIdDateRange()
    {
        return $this->idDateRange;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return PageViewed
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
