<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


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
     * @ORM\Column(name="class_name", type="string", length=50, nullable=false, options={"default"="WebserviceRequest"})
     */
    private $className = 'WebserviceRequest';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_module", type="boolean", nullable=false, options={"default":0})
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
