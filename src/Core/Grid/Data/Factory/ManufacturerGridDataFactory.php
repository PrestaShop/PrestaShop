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
 * Gets data for manufacturer grid
 */
final class ManufacturerGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $manufacturerDataFactory;

    /**
     * @var ImageProviderInterface
     */
    private $manufacturerLogoThumbnailProvider;

    /**
     * @param GridDataFactoryInterface $manufacturerDataFactory
     * @param ImageProviderInterface $manufacturerLogoThumbnailProvider
     */
    public function __construct(
        GridDataFactoryInterface $manufacturerDataFactory,
        ImageProviderInterface $manufacturerLogoThumbnailProvider
    ) {
        $this->manufacturerDataFactory = $manufacturerDataFactory;
        $this->manufacturerLogoThumbnailProvider = $manufacturerLogoThumbnailProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $manufacturerData = $this->manufacturerDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $manufacturerData->getRecords()->all()
        );

        return new GridData(
            new RecordCollection($modifiedRecords),
            $manufacturerData->getRecordsTotal(),
            $manufacturerData->getQuery()
        );
    }

    /**
     * @param array $manufacturers
     *
     * @return array
     */
    private function applyModification(array $manufacturers)
    {
        foreach ($manufacturers as $i => $manufacturer) {
            $manufacturers[$i]['logo'] = $this->manufacturerLogoThumbnailProvider->getPath(
                $manufacturer['id_manufacturer']
            );

            if (null === $manufacturers[$i]['addresses_count']) {
                $manufacturers[$i]['addresses_count'] = '--';
            }
        }

        return $manufacturers;
    }
}
