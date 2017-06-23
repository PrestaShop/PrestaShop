<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerMessageSyncImap
 *
 * @ORM\Table(indexes={@ORM\Index(name="md5_header_index", columns={"md5_header"})})
 * @ORM\Entity
 */
class CustomerMessageSyncImap
{
    /**
     * @var binary
     *
     * @ORM\Column(name="md5_header", type="binary")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $md5Header;



    /**
     * Get md5Header
     *
     * @return binary
     */
    public function getMd5Header()
    {
        return $this->md5Header;
    }
}
