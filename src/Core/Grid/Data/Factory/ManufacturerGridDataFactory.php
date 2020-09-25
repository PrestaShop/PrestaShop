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
