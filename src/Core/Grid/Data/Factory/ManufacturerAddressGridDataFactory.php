<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

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
