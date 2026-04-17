<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\WebserviceKeyId;

/**
 * Editable data for webservice key
 */
class EditableWebserviceKey
{
    /**
     * @var WebserviceKeyId
     */
    private $webserviceKeyId;

    /**
     * @var string
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
    private $resourcePermissions;

    /**
     * @var int[]
     */
    private $associatedShops;

    /**
     * @param WebserviceKeyId $webserviceKeyId
     * @param string $key
     * @param string $description
     * @param bool $status
     * @param array $resourcePermissions
     * @param array $associatedShops
     */
    public function __construct(
        WebserviceKeyId $webserviceKeyId,
        $key,
        $description,
        $status,
        array $resourcePermissions,
        array $associatedShops
    ) {
        $this->webserviceKeyId = $webserviceKeyId;
        $this->key = $key;
        $this->description = $description;
        $this->status = $status;
        $this->resourcePermissions = $resourcePermissions;
        $this->associatedShops = $associatedShops;
    }

    /**
     * @return WebserviceKeyId
     */
    public function getWebserviceKeyId()
    {
        return $this->webserviceKeyId;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
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
    public function getResourcePermissions()
    {
        return $this->resourcePermissions;
    }

    /**
     * @return int[]
     */
    public function getAssociatedShops()
    {
        return $this->associatedShops;
    }
}
