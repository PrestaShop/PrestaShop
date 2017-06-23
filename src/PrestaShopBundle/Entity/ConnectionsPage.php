<?php

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
