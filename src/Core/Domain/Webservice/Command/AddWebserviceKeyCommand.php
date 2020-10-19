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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\Command;

use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\Key;

/**
 * Adds new webservice key which is used to access PrestaShop's API
 */
class AddWebserviceKeyCommand
{
    /**
     * @var Key
     */
    private $key;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $status;

    /**
     * @var array
     */
    private $permissions;

    /**
     * @var array
     */
    private $associatedShops;

    /**
     * @var string
     */
    private $hosts_allowed;

    /**
     * @var bool
     */
    private $hosts_check;

    /**
     * @param string $key
     * @param string $description
     * @param bool $status
     * @param array $permissions
     * @param int[] $associatedShops
     * @param string $hosts_allowed
     * @param bool $hosts_check
     */
    public function __construct(
        $key,
        $description,
        $status,
        array $permissions,
        array $associatedShops,
        string $hosts_allowed = null,
        bool $hosts_check = false
    ) {
        $this->key = new Key($key);
        $this->description = $description;
        $this->status = $status;
        $this->permissions = $permissions;
        $this->associatedShops = $associatedShops;
        $this->hosts_allowed = $hosts_allowed;
        $this->hosts_check = $hosts_check;
    }

    /**
     * @return Key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return array
     */
    public function getAssociatedShops()
    {
        return $this->associatedShops;
    }

    /**
     * @return string
     */
    public function getHostsAllowed(): string
    {
        return $this->hosts_allowed;
    }

    /**
     * @return bool
     */
    public function getHostsCheck(): bool
    {
        return $this->hosts_check;
    }
}
