<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\EditableSupplier;

/**
 * Provides data for suppliers add/edit forms
 */
final class SupplierFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var bool
     */
    private $multistoreEnabled;

    /**
     * @var int[]
     */
    private $defaultShopAssociation;

    /**
     * @var int
     */
    private $contextCountryId;

    /**
     * @param CommandBusInterface $queryBus
     * @param bool $multistoreEnabled
     * @param int[] $defaultShopAssociation
     * @param int $contextCountryId
     */
    public function __construct(
        CommandBusInterface $queryBus,
        $multistoreEnabled,
        array $defaultShopAssociation,
        $contextCountryId
    ) {
        $this->queryBus = $queryBus;
        $this->multistoreEnabled = $multistoreEnabled;
        $this->defaultShopAssociation = $defaultShopAssociation;
        $this->contextCountryId = $contextCountryId;
    }

    /**
     * {@inheritdoc}
     *
     * @throws SupplierException
     */
    public function getData($supplierId)
    {
        /** @var EditableSupplier $editableSupplier */
        $editableSupplier = $this->queryBus->handle(new GetSupplierForEditing((int) $supplierId));

        $data = [
            'name' => $editableSupplier->getName(),
            'description' => $editableSupplier->getLocalizedDescriptions(),
            'phone' => $editableSupplier->getPhone(),
            'mobile_phone' => $editableSupplier->getMobilePhone(),
            'address' => $editableSupplier->getAddress(),
            'address2' => $editableSupplier->getAddress2(),
            'post_code' => $editableSupplier->getPostCode(),
            'city' => $editableSupplier->getCity(),
            'id_country' => $editableSupplier->getCountryId(),
            'id_state' => $editableSupplier->getStateId(),
            'meta_title' => $editableSupplier->getLocalizedMetaTitles(),
            'meta_description' => $editableSupplier->getLocalizedMetaDescriptions(),
            'is_enabled' => $editableSupplier->isEnabled(),
            'dni' => $editableSupplier->getDni(),
        ];

        if ($this->multistoreEnabled) {
            $data['shop_association'] = $editableSupplier->getAssociatedShops();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $data['is_enabled'] = false;
        $data['id_country'] = $this->contextCountryId;

        if ($this->multistoreEnabled) {
            $data['shop_association'] = $this->defaultShopAssociation;
        }

        return $data;
    }
}
