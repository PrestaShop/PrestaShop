<?php

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
