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
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Gets data for catalog price rule grid
 */
final class CatalogPriceRuleGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $priceRuleDataFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param GridDataFactoryInterface $priceRuleDataFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(GridDataFactoryInterface $priceRuleDataFactory, TranslatorInterface $translator)
    {
        $this->priceRuleDataFactory = $priceRuleDataFactory;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $priceRuleData = $this->priceRuleDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $priceRuleData->getRecords()->all()
        );

        return new GridData(
            new RecordCollection($modifiedRecords),
            $priceRuleData->getRecordsTotal(),
            $priceRuleData->getQuery()
        );
    }

    /**
     * @param array $priceRules
     *
     * @return array
     */
    private function applyModification(array $priceRules)
    {
        foreach ($priceRules as $i => $priceRule) {
            if ($priceRules[$i]['reduction_type'] === 'amount') {
                $priceRules[$i]['reduction_type'] = $this->translator->trans('Amount', [], 'Admin.Global');
                continue;
            }

            $priceRules[$i]['reduction_type'] = $this->translator->trans('Percentage', [], 'Admin.Global');
        }

        return $priceRules;
    }
}
