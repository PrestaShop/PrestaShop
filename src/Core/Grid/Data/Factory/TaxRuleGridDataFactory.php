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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Domain\TaxRule\TaxRuleSettings;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TaxRuleGridDataFactory gets data for TaxRule grid.
 */
class TaxRuleGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $doctrineTaxRuleDataFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param GridDataFactoryInterface $doctrineTaxRuleDataFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(
        GridDataFactoryInterface $doctrineTaxRuleDataFactory,
        TranslatorInterface $translator
    ) {
        $this->doctrineTaxRuleDataFactory = $doctrineTaxRuleDataFactory;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $taxRuleData = $this->doctrineTaxRuleDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $taxRuleData->getRecords()->all()
        );

        return new GridData(
            new RecordCollection($modifiedRecords),
            $taxRuleData->getRecordsTotal(),
            $taxRuleData->getQuery()
        );
    }

    /**
     * @param array $records
     *
     * @return array
     */
    private function applyModification(array $records): array
    {
        foreach ($records as $i => $record) {
            switch ($record['behavior']) {
                case TaxRuleSettings::BEHAVIOR_TAX_ONLY:
                    $records[$i]['behavior'] = $this->translator->trans('This tax only', [], 'Admin.International.Feature');
                    break;
                case TaxRuleSettings::BEHAVIOR_COMBINE:
                    $records[$i]['behavior'] = $this->translator->trans('Combine', [], 'Admin.International.Feature');
                    break;
                case TaxRuleSettings::BEHAVIOR_ONE_AFTER_ANOTHER:
                    $records[$i]['behavior'] = $this->translator->trans('One after another', [], 'Admin.International.Feature');
                    break;
            }

            $records[$i]['rate'] = sprintf('%.3f%%', $record['rate']);
        }

        return $records;
    }
}
