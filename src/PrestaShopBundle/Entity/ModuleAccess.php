<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ModuleAccess
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ModuleAccess
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_profile", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProfile;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_authorization_role", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idAuthorizationRole;



    /**
     * Set idProfile
     *
     * @param integer $idProfile
     *
     * @return ModuleAccess
     */
    public function setIdProfile($idProfile)
    {
        $this->idProfile = $idProfile;

        return $this;
    }

    /**
     * Get idProfile
     *
     * @return integer
     */
    public function getIdProfile()
    {
        return $this->idProfile;
    }

    /**
     * Set idAuthorizationRole
     *
     * @param integer $idAuthorizationRole
     *
     * @return ModuleAccess
     */
    public function setIdAuthorizationRole($idAuthorizationRole)
    {
        $this->idAuthorizationRole = $idAuthorizationRole;

        return $this;
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
