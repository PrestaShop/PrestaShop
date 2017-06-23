<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImportMatch
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ImportMatch
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="match", type="text", length=65535, nullable=false)
     */
    private $match;

    /**
     * @var integer
     *
     * @ORM\Column(name="skip", type="integer", nullable=false)
     */
    private $skip;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_import_match", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idImportMatch;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return ImportMatch
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set match
     *
     * @param string $match
     *
     * @return ImportMatch
     */
    public function setMatch($match)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * Get match
     *
     * @return string
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * Set skip
     *
     * @param integer $skip
     *
     * @return ImportMatch
     */
    public function setSkip($skip)
    {
        $this->skip = $skip;

        return $this;
    }

    /**
     * Get skip
     *
     * @return integer
     */
    public function getSkip()
    {
        return $this->skip;
    }

    /**
     * Get idImportMatch
     *
     * @return integer
     */
    public function getIdImportMatch()
    {
        return $this->idImportMatch;
    }
}
