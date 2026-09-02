<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        foreach ($priceRules as &$priceRule) {
            foreach ($priceRule as &$value) {
                if ($value === null) {
                    $value = '--';
                }
            }

            Reduction::TYPE_AMOUNT === $priceRule['reduction_type'] ?
                $priceRule['reduction_type'] = $this->translator->trans('Amount', [], 'Admin.Global') :
                $priceRule['reduction_type'] = $this->translator->trans('Percentage', [], 'Admin.Global');
        }

        return $priceRules;
    }
}
