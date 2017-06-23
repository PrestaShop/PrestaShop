<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReferrerCache
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ReferrerCache
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_connections_source", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idConnectionsSource;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_referrer", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idReferrer;



    /**
     * Set idConnectionsSource
     *
     * @param integer $idConnectionsSource
     *
     * @return ReferrerCache
     */
    public function setIdConnectionsSource($idConnectionsSource)
    {
        $this->idConnectionsSource = $idConnectionsSource;

        return $this;
    }

    /**
     * Get idConnectionsSource
     *
     * @return integer
     */
    public function getIdConnectionsSource()
    {
        return $this->idConnectionsSource;
    }

    /**
     * Set idReferrer
     *
     * @param integer $idReferrer
     *
     * @return ReferrerCache
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
}
