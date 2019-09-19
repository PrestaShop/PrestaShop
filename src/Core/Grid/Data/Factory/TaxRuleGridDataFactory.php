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

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Gets and modifies data for supplier grid.
 */
final class TaxRuleGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $dataFactory;

    /**
     * @var FormChoiceProviderInterface
     */
    private $behaviorChoiceProvider;

    /**
     * @param GridDataFactoryInterface $dataFactory
     * @param FormChoiceProviderInterface $behaviorChoiceProvider
     */
    public function __construct(
        GridDataFactoryInterface $dataFactory,
        FormChoiceProviderInterface $behaviorChoiceProvider
    ) {
        $this->dataFactory = $dataFactory;
        $this->behaviorChoiceProvider = $behaviorChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $data = $this->dataFactory->getData($searchCriteria);
        $records = $data->getRecords()->all();

        $behaviorChoiceFlipped = array_flip($this->behaviorChoiceProvider->getChoices());

        foreach ($records as &$record) {
            if (!$record['state_name']) {
                $record['state_name'] = '--';
            }

            if (!$record['description']) {
                $record['description'] = '--';
            }

            if (!$record['rate']) {
                $record['rate'] = '--';
            }

            if (!$record['zipcode_from'] && !$record['zipcode_to']) {
                $record['zipcode'] = '--';
            } else {
                $record['zipcode'] = $record['zipcode_from'] . '-' . $record['zipcode_to'];
            }

            $behavior = '--';

            if (array_key_exists($record['behavior'], $behaviorChoiceFlipped)) {
                $behavior = $behaviorChoiceFlipped[$record['behavior']];
            }

            $record['behavior'] = $behavior;
        }

        return new GridData(
            new RecordCollection($records),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }
}
