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

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;

/**
 * Class CustomerOrderGridDataFactoryDecorator decorates data from customer ordesrs doctrine data factory.
 */
final class CustomerOrderGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $customerOrderDoctrineGridDataFactory;

    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * @var string
     */
    private $contextCurrencyIsoCode;

    /**
     * @param GridDataFactoryInterface $customerOrderDoctrineGridDataFactory
     * @param LocaleInterface $locale
     * @param string $contextCurrencyIsoCode
     */
    public function __construct(
        GridDataFactoryInterface $customerOrderDoctrineGridDataFactory,
        LocaleInterface $locale,
        $contextCurrencyIsoCode
    ) {
        $this->customerOrderDoctrineGridDataFactory = $customerOrderDoctrineGridDataFactory;
        $this->locale = $locale;
        $this->contextCurrencyIsoCode = $contextCurrencyIsoCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $customerData = $this->customerOrderDoctrineGridDataFactory->getData($searchCriteria);

        $records = $this->applyModifications($customerData->getRecords());

        return new GridData(
            $records,
            $customerData->getRecordsTotal(),
            $customerData->getQuery()
        );
    }

    /**
     * @param RecordCollectionInterface $records
     *
     * @return RecordCollection
     */
    private function applyModifications(RecordCollectionInterface $records)
    {
        $modifiedRecords = [];
        foreach ($records as $r) {
            if (!empty($r['total_paid_tax_incl'])) {
                $r['total_paid_tax_incl'] = $this->locale->formatPrice(
                    $r['total_paid_tax_incl'],
                    $this->contextCurrencyIsoCode
                );
            }

            if (!empty($r['status'])) {
                $r['status'] = '<span class="badge badge-' .
                ($r['valid'] == 1 ? 'success' : 'danger') .
                ' rounded">' . $r['status'] . '</span>';
            }

            if (empty($r['nb_products'])) {
                $r['nb_products'] = '0';
            }

            $modifiedRecords[] = $r;
        }

        return new RecordCollection($modifiedRecords);
    }
}
