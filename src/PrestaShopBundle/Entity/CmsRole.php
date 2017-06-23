<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CmsRole
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\Entity
 */
class CmsRole
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cms_role", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCmsRole;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cms", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCms;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return CmsRole
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
     * Set idCmsRole
     *
     * @param integer $idCmsRole
     *
     * @return CmsRole
     */
    public function setIdCmsRole($idCmsRole)
    {
        $this->idCmsRole = $idCmsRole;

        return $this;
    }

    /**
     * Get idCmsRole
     *
     * @return integer
     */
    public function getIdCmsRole()
    {
        return $this->idCmsRole;
    }

    /**
     * Set idCms
     *
     * @param integer $idCms
     *
     * @return CmsRole
     */
    public function setIdCms($idCms)
    {
        $this->idCms = $idCms;

        return $this;
    }

    /**
     * Get idCms
     *
     * @return integer
     */
    public function getIdCms()
    {
        return $this->idCms;
    }
}
