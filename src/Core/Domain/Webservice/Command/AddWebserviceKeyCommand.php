<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @var bool
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
     * @param string $key
     * @param string $description
     * @param bool $status
     * @param array $permissions
     * @param int[] $associatedShops
     */
    public function __construct(
        $key,
        $description,
        $status,
        array $permissions,
        array $associatedShops
    ) {
        $this->key = new Key($key);
        $this->description = $description;
        $this->status = $status;
        $this->permissions = $permissions;
        $this->associatedShops = $associatedShops;
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
     * @return bool
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
}
