<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebservicePermission
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="resource_2", columns={"resource", "method", "id_webservice_account"})}, indexes={@ORM\Index(name="resource", columns={"resource"}), @ORM\Index(name="method", columns={"method"}), @ORM\Index(name="id_webservice_account", columns={"id_webservice_account"})})
 * @ORM\Entity
 */
class WebservicePermission
{
    /**
     * @var string
     *
     * @ORM\Column(name="resource", type="string", length=50, nullable=false)
     */
    private $resource;

    /**
     * @var string
     *
     * @ORM\Column(name="method", type="string", nullable=false)
     */
    private $method;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_webservice_account", type="integer", nullable=false)
     */
    private $idWebserviceAccount;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_webservice_permission", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idWebservicePermission;



    /**
     * Set resource
     *
     * @param string $resource
     *
     * @return WebservicePermission
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set method
     *
     * @param string $method
     *
     * @return WebservicePermission
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set idWebserviceAccount
     *
     * @param integer $idWebserviceAccount
     *
     * @return WebservicePermission
     */
    public function setIdWebserviceAccount($idWebserviceAccount)
    {
        $this->idWebserviceAccount = $idWebserviceAccount;

        return $this;
    }

    /**
     * Get idWebserviceAccount
     *
     * @return integer
     */
    public function getIdWebserviceAccount()
    {
        return $this->idWebserviceAccount;
    }

    /**
     * Get idWebservicePermission
     *
     * @return integer
     */
    public function getIdWebservicePermission()
    {
        return $this->idWebservicePermission;
    }
}
