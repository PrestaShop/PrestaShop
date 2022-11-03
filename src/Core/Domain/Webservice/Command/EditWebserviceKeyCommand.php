<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\Command;

use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\Key;
use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\WebserviceKeyId;

/**
 * Edits webservice key data
 */
class EditWebserviceKeyCommand
{
    /**
     * @var WebserviceKeyId
     */
    private $webserviceKeyId;

    /**
     * @var Key|null
     */
    private $key;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var bool|null
     */
    private $status;

    /**
     * @var array|null
     */
    private $permissions;

    /**
     * @var int[]|null
     */
    private $shopAssociation;

    /**
     * @param int $webserviceKeyId
     */
    public function __construct($webserviceKeyId)
    {
        $this->webserviceKeyId = new WebserviceKeyId($webserviceKeyId);
    }

    /**
     * @return WebserviceKeyId
     */
    public function getWebserviceKeyId()
    {
        return $this->webserviceKeyId;
    }

    /**
     * @return Key|null
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return self
     */
    public function setKey($key)
    {
        $this->key = new Key($key);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param array $permissions
     *
     * @return self
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * @return int[]|null
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }

    /**
     * @param int[] $shopAssociation
     *
     * @return self
     */
    public function setShopAssociation(array $shopAssociation)
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }
}
