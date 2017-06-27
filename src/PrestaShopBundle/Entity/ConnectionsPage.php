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
 * ConnectionsPage
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ConnectionsPage
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_end", type="datetime", nullable=true)
     */
    private $timeEnd;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_connections", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idConnections;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_page", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idPage;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_start", type="datetime")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $timeStart;



    /**
     * Set timeEnd
     *
     * @param \DateTime $timeEnd
     *
     * @return ConnectionsPage
     */
    public function setTimeEnd($timeEnd)
    {
        $this->timeEnd = $timeEnd;

        return $this;
    }

    /**
     * Get timeEnd
     *
     * @return \DateTime
     */
    public function getTimeEnd()
    {
        return $this->timeEnd;
    }

    /**
     * Set idConnections
     *
     * @param integer $idConnections
     *
     * @return ConnectionsPage
     */
    public function setIdConnections($idConnections)
    {
        $this->idConnections = $idConnections;

        return $this;
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

    /**
     * Set idPage
     *
     * @param integer $idPage
     *
     * @return ConnectionsPage
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
     * Set timeStart
     *
     * @param \DateTime $timeStart
     *
     * @return ConnectionsPage
     */
    public function setTimeStart($timeStart)
    {
        $this->timeStart = $timeStart;

        return $this;
    }

    /**
     * Get timeStart
     *
     * @return \DateTime
     */
    public function getTimeStart()
    {
        return $this->timeStart;
    }
}
