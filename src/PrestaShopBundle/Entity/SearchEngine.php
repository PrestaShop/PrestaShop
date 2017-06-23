<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SearchEngine
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SearchEngine
{
    /**
     * @var string
     *
     * @ORM\Column(name="server", type="string", length=64, nullable=false)
     */
    private $server;

    /**
     * @var string
     *
     * @ORM\Column(name="getvar", type="string", length=16, nullable=false)
     */
    private $getvar;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_search_engine", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSearchEngine;



    /**
     * Set server
     *
     * @param string $server
     *
     * @return SearchEngine
     */
    public function setServer($server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Get server
     *
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Set getvar
     *
     * @param string $getvar
     *
     * @return SearchEngine
     */
    public function setGetvar($getvar)
    {
        $this->getvar = $getvar;

        return $this;
    }

    /**
     * Get getvar
     *
     * @return string
     */
    public function getGetvar()
    {
        return $this->getvar;
    }

    /**
     * Get idSearchEngine
     *
     * @return integer
     */
    public function getIdSearchEngine()
    {
        return $this->idSearchEngine;
    }
}
