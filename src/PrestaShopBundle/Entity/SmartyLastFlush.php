<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SmartyLastFlush
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SmartyLastFlush
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_flush", type="datetime", nullable=false)
     */
    private $lastFlush = '0000-00-00 00:00:00';

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $type;



    /**
     * Set lastFlush
     *
     * @param \DateTime $lastFlush
     *
     * @return SmartyLastFlush
     */
    public function setLastFlush($lastFlush)
    {
        $this->lastFlush = $lastFlush;

        return $this;
    }

    /**
     * Get lastFlush
     *
     * @return \DateTime
     */
    public function getLastFlush()
    {
        return $this->lastFlush;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
