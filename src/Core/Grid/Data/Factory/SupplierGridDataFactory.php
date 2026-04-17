<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;

/**
 * Class SupplierGridDataFactory gets data for supplier grid.
 */
final class SupplierGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $supplierDataFactory;

    /**
     * @var ImageProviderInterface
     */
    private $supplierLogoImageProvider;

    /**
     * @param GridDataFactoryInterface $supplierDataFactory
     * @param ImageProviderInterface $supplierLogoImageProvider
     */
    public function __construct(
        GridDataFactoryInterface $supplierDataFactory,
        ImageProviderInterface $supplierLogoImageProvider
    ) {
        $this->supplierDataFactory = $supplierDataFactory;
        $this->supplierLogoImageProvider = $supplierLogoImageProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $supplierData = $this->supplierDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $supplierData->getRecords()->all()
        );

        return new GridData(
            new RecordCollection($modifiedRecords),
            $supplierData->getRecordsTotal(),
            $supplierData->getQuery()
        );
    }

    /**
     * @param array $suppliers
     *
     * @return array
     */
    private function applyModification(array $suppliers)
    {
        foreach ($suppliers as $i => $supplier) {
            $suppliers[$i]['logo'] = $this->supplierLogoImageProvider->getPath($supplier['id_supplier']);
        }

        return $suppliers;
    }
}
