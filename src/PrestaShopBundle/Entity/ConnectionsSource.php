<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConnectionsSource
 *
 * @ORM\Table(indexes={@ORM\Index(name="connections", columns={"id_connections"}), @ORM\Index(name="orderby", columns={"date_add"}), @ORM\Index(name="http_referer", columns={"http_referer"}), @ORM\Index(name="request_uri", columns={"request_uri"})})
 * @ORM\Entity
 */
class ConnectionsSource
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_connections", type="integer", nullable=false)
     */
    private $idConnections;

    /**
     * @var string
     *
     * @ORM\Column(name="http_referer", type="string", length=255, nullable=true)
     */
    private $httpReferer;

    /**
     * @var string
     *
     * @ORM\Column(name="request_uri", type="string", length=255, nullable=true)
     */
    private $requestUri;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", length=255, nullable=true)
     */
    private $keywords;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_connections_source", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idConnectionsSource;



    /**
     * Set idConnections
     *
     * @param integer $idConnections
     *
     * @return ConnectionsSource
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
     * Set httpReferer
     *
     * @param string $httpReferer
     *
     * @return ConnectionsSource
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
     * Set requestUri
     *
     * @param string $requestUri
     *
     * @return ConnectionsSource
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;

        return $this;
    }

    /**
     * Get requestUri
     *
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     *
     * @return ConnectionsSource
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return ConnectionsSource
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
     * Get idConnectionsSource
     *
     * @return integer
     */
    public function getIdConnectionsSource()
    {
        return $this->idConnectionsSource;
    }
}
