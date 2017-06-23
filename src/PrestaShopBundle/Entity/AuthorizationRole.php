<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuthorizationRole
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="slug", columns={"slug"})})
 * @ORM\Entity
 */
class AuthorizationRole
{
    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_authorization_role", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idAuthorizationRole;



    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return AuthorizationRole
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get idAuthorizationRole
     *
     * @return integer
     */
    public function getIdAuthorizationRole()
    {
        return $this->idAuthorizationRole;
    }
}
