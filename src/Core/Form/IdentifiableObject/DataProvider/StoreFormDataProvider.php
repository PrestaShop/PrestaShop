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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Query\GetStoreForEditing;
use PrestaShop\PrestaShop\Core\Domain\Store\QueryResult\StoreForEditing;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class StoreFormDataProvider is responsible for providing form data for stores by store id or by giving default
 * values.
 */
final class StoreFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @var DataTransformerInterface
     */
    private $stringArrayToIntegerArrayDataTransformer;

    /**
     * @param CommandBusInterface $queryBus
     * @param DataTransformerInterface $stringArrayToIntegerArrayDataTransformer
     * @param int[] $contextShopIds
     */
    public function __construct(
        CommandBusInterface $queryBus,
        DataTransformerInterface $stringArrayToIntegerArrayDataTransformer,
        array $contextShopIds
    ) {
        $this->queryBus = $queryBus;
        $this->contextShopIds = $contextShopIds;
        $this->stringArrayToIntegerArrayDataTransformer = $stringArrayToIntegerArrayDataTransformer;
    }

    /**
     * {@inheritdoc}
     *
     * @throws StoreException
     */
    public function getData($storeId)
    {
        /** @var StoreForEditing $storeForEditing */
        $storeForEditing = $this->queryBus->handle(new GetStoreForEditing($storeId));

        return [
            'name' => $storeForEditing->getLocalisedNames(),
            'address1' => $storeForEditing->getLocalisedAddresses1(),
            'address2' => $storeForEditing->getLocalisedAddresses2(),
            'postcode' => $storeForEditing->getPostcode(),
            'city' => $storeForEditing->getCity(),
            'country' => $storeForEditing->getCountryId(),
            'state' => $storeForEditing->getStateId(),
            'latitude' => $storeForEditing->getLatitude(),
            'longitude' => $storeForEditing->getLongitude(),
            'phone' => $storeForEditing->getPhone(),
            'getFax' => $storeForEditing->getFax(),
            'email' => $storeForEditing->getEmail(),
            'note' => $storeForEditing->getLocalisedNotes(),
            'hours' => $storeForEditing->getHours(),
            'shop_association' => $storeForEditing->getShopAssociation(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $shopIds = $this->stringArrayToIntegerArrayDataTransformer->reverseTransform($this->contextShopIds);

        return [
            'shop_association' => $shopIds,
        ];
    }
}
