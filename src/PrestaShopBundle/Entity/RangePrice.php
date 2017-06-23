<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RangePrice
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_carrier", columns={"id_carrier", "delimiter1", "delimiter2"})})
 * @ORM\Entity
 */
class RangePrice
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_carrier", type="integer", nullable=false)
     */
    private $idCarrier;

    /**
     * @var string
     *
     * @ORM\Column(name="delimiter1", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $delimiter1;

    /**
     * @var string
     *
     * @ORM\Column(name="delimiter2", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $delimiter2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_range_price", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idRangePrice;



    /**
     * Set idCarrier
     *
     * @param integer $idCarrier
     *
     * @return RangePrice
     */
    public function setIdCarrier($idCarrier)
    {
        $this->idCarrier = $idCarrier;

        return $this;
    }

    /**
     * Get idCarrier
     *
     * @return integer
     */
    public function getIdCarrier()
    {
        return $this->idCarrier;
    }

    /**
     * Set delimiter1
     *
     * @param string $delimiter1
     *
     * @return RangePrice
     */
    public function setDelimiter1($delimiter1)
    {
        $this->delimiter1 = $delimiter1;

        return $this;
    }

    /**
     * Get delimiter1
     *
     * @return string
     */
    public function getDelimiter1()
    {
        return $this->delimiter1;
    }

    /**
     * Set delimiter2
     *
     * @param string $delimiter2
     *
     * @return RangePrice
     */
    public function setDelimiter2($delimiter2)
    {
        $this->delimiter2 = $delimiter2;

        return $this;
    }

    /**
     * Get delimiter2
     *
     * @return string
     */
    public function getDelimiter2()
    {
        return $this->delimiter2;
    }

    /**
     * Get idRangePrice
     *
     * @return integer
     */
    public function getIdRangePrice()
    {
        return $this->idRangePrice;
    }
}
