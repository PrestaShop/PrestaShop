<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Data\GridDataInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Decorates discount grid data to display a fallback when expiration date is null,
 * and to label core discount types from the translation catalogue.
 */
final class DiscountGridDataFactoryDecorator implements GridDataFactoryInterface
{
    public function __construct(
        private readonly GridDataFactoryInterface $discountGridDataFactory,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria): GridDataInterface
    {
        $data = $this->discountGridDataFactory->getData($searchCriteria);
        $records = $data->getRecords()->all();

        foreach ($records as &$record) {
            if (DateTimeUtil::isNull($record['date_to'] ?? null)) {
                $record['date_to'] = $this->translator->trans('No end date', [], 'Admin.Catalog.Feature');
            }
            // The query reads the label from cart_rule_type_lang, which holds the default language's
            // text for every language because installing a language copies those rows and no
            // per-language seed replaces them. Core types are therefore labelled from the catalogue;
            // a type provided by a module keeps its stored name, the only source there is for it.
            if (isset($record['discount_type'], DiscountType::CORE_LABELS[$record['discount_type']])) {
                $record['discount_type_label'] = $this->translator->trans(
                    DiscountType::CORE_LABELS[$record['discount_type']],
                    [],
                    'Admin.Catalog.Feature'
                );
            }
        }

        return new GridData(
            new RecordCollection($records),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }
}
