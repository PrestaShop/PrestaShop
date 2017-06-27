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
 * MemcachedServers
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class MemcachedServers
{
    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=254, nullable=false)
     */
    private $ip;

    /**
     * @var integer
     *
     * @ORM\Column(name="port", type="integer", nullable=false)
     */
    private $port;

    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="integer", nullable=false)
     */
    private $weight;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_memcached_server", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idMemcachedServer;



    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return MemcachedServers
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set port
     *
     * @param integer $port
     *
     * @return MemcachedServers
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get port
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     *
     * @return MemcachedServers
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Get idMemcachedServer
     *
     * @return integer
     */
    public function getIdMemcachedServer()
    {
        return $this->idMemcachedServer;
    }
}
