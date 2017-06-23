<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Meta
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="page", columns={"page"})})
 * @ORM\Entity
 */
class Meta
{
    /**
     * @var string
     *
     * @ORM\Column(name="page", type="string", length=64, nullable=false)
     */
    private $page;

    /**
     * @var boolean
     *
     * @ORM\Column(name="configurable", type="boolean", nullable=false)
     */
    private $configurable = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_meta", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idMeta;



    /**
     * Set page
     *
     * @param string $page
     *
     * @return Meta
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set configurable
     *
     * @param boolean $configurable
     *
     * @return Meta
     */
    public function setConfigurable($configurable)
    {
        $this->configurable = $configurable;

        return $this;
    }

    /**
     * Get configurable
     *
     * @return boolean
     */
    public function getConfigurable()
    {
        return $this->configurable;
    }

    /**
     * Get idMeta
     *
     * @return integer
     */
    public function getIdMeta()
    {
        return $this->idMeta;
    }
}
