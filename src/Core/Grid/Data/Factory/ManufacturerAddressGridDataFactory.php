<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;

/**
 * Gets data for manufacturer addresses grid
 */
class ManufacturerAddressGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $manufacturerAddressDataFactory;

    /**
     * @param GridDataFactoryInterface $manufacturerAddressDataFactory
     */
    public function __construct(GridDataFactoryInterface $manufacturerAddressDataFactory)
    {
        $this->manufacturerAddressDataFactory = $manufacturerAddressDataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $addresses = $this->manufacturerAddressDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $addresses->getRecords()->all()
        );

        return new GridData(
            new RecordCollection($modifiedRecords),
            $addresses->getRecordsTotal(),
            $addresses->getQuery()
        );
    }

    /**
     * @param array $addresses
     *
     * @return array
     */
    private function applyModification(array $addresses)
    {
        $modifiedAddresses = [];
        foreach ($addresses as $address) {
            if (null === $address['name']) {
                $address['name'] = '--';
            }
            $modifiedAddresses[] = $address;
        }

        return $modifiedAddresses;
    }
}
