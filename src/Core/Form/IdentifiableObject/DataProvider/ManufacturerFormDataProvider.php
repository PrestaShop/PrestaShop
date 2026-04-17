<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\EditableManufacturer;

/**
 * Provides data for manufacturers add/edit forms
 */
final class ManufacturerFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @var bool
     */
    private $multistoreEnabled;

    /**
     * @var int[]
     */
    private $defaultShopAssociation;

    /**
     * @param CommandBusInterface $bus
     * @param bool $multistoreEnabled
     * @param int[] $defaultShopAssociation
     */
    public function __construct(
        CommandBusInterface $bus,
        $multistoreEnabled,
        array $defaultShopAssociation
    ) {
        $this->bus = $bus;
        $this->multistoreEnabled = $multistoreEnabled;
        $this->defaultShopAssociation = $defaultShopAssociation;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($manufacturerId)
    {
        /** @var EditableManufacturer $editableManufacturer */
        $editableManufacturer = $this->bus->handle(new GetManufacturerForEditing((int) $manufacturerId));

        $data = [
            'name' => $editableManufacturer->getName(),
            'short_description' => $editableManufacturer->getLocalizedShortDescriptions(),
            'description' => $editableManufacturer->getLocalizedDescriptions(),
            'meta_title' => $editableManufacturer->getLocalizedMetaTitles(),
            'meta_description' => $editableManufacturer->getLocalizedMetaDescriptions(),
            'is_enabled' => $editableManufacturer->isEnabled(),
        ];

        if ($this->multistoreEnabled) {
            $data['shop_association'] = $editableManufacturer->getAssociatedShops();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $data['is_enabled'] = true;

        if ($this->multistoreEnabled) {
            $data['shop_association'] = $this->defaultShopAssociation;
        }

        return $data;
    }
}
