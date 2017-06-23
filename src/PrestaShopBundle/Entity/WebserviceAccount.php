<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebserviceAccount
 *
 * @ORM\Table(indexes={@ORM\Index(name="key", columns={"key"})})
 * @ORM\Entity
 */
class WebserviceAccount
{
    /**
     * @var string
     *
     * @ORM\Column(name="key", type="string", length=32, nullable=false)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="class_name", type="string", length=50, nullable=false)
     */
    private $className = 'WebserviceRequest';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_module", type="boolean", nullable=false)
     */
    private $isModule = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="module_name", type="string", length=50, nullable=true)
     */
    private $moduleName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_webservice_account", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idWebserviceAccount;



    /**
     * Set key
     *
     * @param string $key
     *
     * @return WebserviceAccount
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return WebserviceAccount
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set className
     *
     * @param string $className
     *
     * @return WebserviceAccount
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Get className
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Set isModule
     *
     * @param boolean $isModule
     *
     * @return WebserviceAccount
     */
    public function setIsModule($isModule)
    {
        $this->isModule = $isModule;

        return $this;
    }

    /**
     * Get isModule
     *
     * @return boolean
     */
    public function getIsModule()
    {
        return $this->isModule;
    }

    /**
     * Set moduleName
     *
     * @param string $moduleName
     *
     * @return WebserviceAccount
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    /**
     * Get moduleName
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return WebserviceAccount
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
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
}
