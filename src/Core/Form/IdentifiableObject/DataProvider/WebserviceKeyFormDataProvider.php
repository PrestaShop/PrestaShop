<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Query\GetWebserviceKeyForEditing;
use PrestaShop\PrestaShop\Core\Domain\Webservice\QueryResult\EditableWebserviceKey;

/**
 * Provides data for webservice key form
 */
final class WebserviceKeyFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var int[]
     */
    private $shopIds;

    /**
     * @param CommandBusInterface $queryBus
     * @param int[] $shopIds
     */
    public function __construct(CommandBusInterface $queryBus, array $shopIds)
    {
        $this->queryBus = $queryBus;
        $this->shopIds = $shopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($webserviceKeyId)
    {
        /** @var EditableWebserviceKey $editableWebserviceKey */
        $editableWebserviceKey = $this->queryBus->handle(new GetWebserviceKeyForEditing($webserviceKeyId));

        return [
            'key' => $editableWebserviceKey->getKey(),
            'description' => $editableWebserviceKey->getDescription(),
            'status' => $editableWebserviceKey->getStatus(),
            'permissions' => $this->normalizeResourcePermissions(
                $editableWebserviceKey->getResourcePermissions()
            ),
            'shop_association' => $editableWebserviceKey->getAssociatedShops(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'status' => true,
            'shop_association' => $this->shopIds,
        ];
    }

    /**
     * Normalizes resource permissions to be in format that is accepted by form
     *
     * @param array $resourcePermissions
     *
     * @return array
     */
    private function normalizeResourcePermissions(array $resourcePermissions)
    {
        $normalizedResourcePermissions = [];

        foreach ($resourcePermissions as $resource => $permissions) {
            foreach ($permissions as $permission) {
                $normalizedResourcePermissions[$permission][] = $resource;
            }
        }

        return $normalizedResourcePermissions;
    }
}
