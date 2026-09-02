<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Image\ImageProviderInterface;

/**
 * Gets data for carrier grid
 */
class CarrierGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $carrierDataFactory;

    /**
     * @var ImageProviderInterface
     */
    private $carrierLogoProvider;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param GridDataFactoryInterface $carrierDataFactory
     * @param ImageProviderInterface $carrierLogoProvider
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        GridDataFactoryInterface $carrierDataFactory,
        ImageProviderInterface $carrierLogoProvider,
        ConfigurationInterface $configuration
    ) {
        $this->carrierDataFactory = $carrierDataFactory;
        $this->carrierLogoProvider = $carrierLogoProvider;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $carrierData = $this->carrierDataFactory->getData($searchCriteria);
        $modifiedRecords = $this->applyModifications($carrierData->getRecords()->all());

        return new GridData(
            new RecordCollection($modifiedRecords),
            $carrierData->getRecordsTotal(),
            $carrierData->getQuery()
        );
    }

    /**
     * Add logo column to grid.
     *
     * @param array $carriers
     *
     * @return array
     */
    private function applyModifications(array $carriers): array
    {
        $carrierDefaultName = str_replace(
            ['#', ';'],
            '',
            $this->configuration->get('PS_SHOP_NAME')
        );

        foreach ($carriers as $i => $carrier) {
            $carriers[$i]['logo'] = $this->carrierLogoProvider->getPath($carrier['id_carrier']);

            if ($carrier['name'] === '0') {
                $carriers[$i]['name'] = $carrierDefaultName;
            }
        }

        return $carriers;
    }
}
