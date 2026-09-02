<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
