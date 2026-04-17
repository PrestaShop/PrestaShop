<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides data for credit slip grid
 */
final class CreditSlipGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $creditSlipDataFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param GridDataFactoryInterface $creditSlipDataFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(
        GridDataFactoryInterface $creditSlipDataFactory,
        TranslatorInterface $translator
    ) {
        $this->creditSlipDataFactory = $creditSlipDataFactory;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $creditSlipData = $this->creditSlipDataFactory->getData($searchCriteria);

        $modifiedRecords = $this->applyModification(
            $creditSlipData->getRecords()->all()
        );

        return new GridData(
            new RecordCollection($modifiedRecords),
            $creditSlipData->getRecordsTotal(),
            $creditSlipData->getQuery()
        );
    }

    /**
     * @param array $creditSlips
     *
     * @return array
     */
    private function applyModification(array $creditSlips)
    {
        foreach ($creditSlips as $i => $creditSlip) {
            $creditSlips[$i]['link_value'] = $this->translator->trans(
                'Download credit slip', [], 'Admin.Orderscustomers.Feature'
            );
        }

        return $creditSlips;
    }
}
