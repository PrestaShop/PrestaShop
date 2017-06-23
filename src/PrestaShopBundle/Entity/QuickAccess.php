<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuickAccess
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class QuickAccess
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="new_window", type="boolean", nullable=false)
     */
    private $newWindow = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, nullable=false)
     */
    private $link;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_quick_access", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idQuickAccess;



    /**
     * Set newWindow
     *
     * @param boolean $newWindow
     *
     * @return QuickAccess
     */
    public function setNewWindow($newWindow)
    {
        $this->newWindow = $newWindow;

        return $this;
    }

    /**
     * Get newWindow
     *
     * @return boolean
     */
    public function getNewWindow()
    {
        return $this->newWindow;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return QuickAccess
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Get idQuickAccess
     *
     * @return integer
     */
    public function getIdQuickAccess()
    {
        return $this->idQuickAccess;
    }
}
