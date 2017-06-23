<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


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
