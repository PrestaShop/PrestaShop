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
     * @ORM\Column(name="method", type="string", nullable=false, columnDefinition="ENUM('GET','POST','PUT','DELETE','HEAD')")
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
