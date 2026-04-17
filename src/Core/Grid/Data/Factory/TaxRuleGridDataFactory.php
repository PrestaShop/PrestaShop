<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
