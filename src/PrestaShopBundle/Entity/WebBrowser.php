<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebBrowser
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class WebBrowser
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_web_browser", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idWebBrowser;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return WebBrowser
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
     * Get idWebBrowser
     *
     * @return integer
     */
    public function getIdWebBrowser()
    {
        return $this->idWebBrowser;
    }
}
