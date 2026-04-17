<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForViewing;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;

/**
 * Class CustomerCartGridDataFactoryDecorator decorates data from customer carts doctrine data factory.
 */
final class CustomerCartGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $customerCartDoctrineGridDataFactory;

    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * @var string
     */
    private $contextCurrencyIsoCode;

    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param GridDataFactoryInterface $customerCartDoctrineGridDataFactory
     * @param LocaleInterface $locale
     * @param string $contextCurrencyIsoCode
     * @param CommandBusInterface $queryBus
     */
    public function __construct(
        GridDataFactoryInterface $customerCartDoctrineGridDataFactory,
        LocaleInterface $locale,
        $contextCurrencyIsoCode,
        CommandBusInterface $queryBus
    ) {
        $this->customerCartDoctrineGridDataFactory = $customerCartDoctrineGridDataFactory;
        $this->locale = $locale;
        $this->contextCurrencyIsoCode = $contextCurrencyIsoCode;
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $customerData = $this->customerCartDoctrineGridDataFactory->getData($searchCriteria);

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
        $modifiedRecord = [];

        foreach ($records as $r) {
            $cartForViewing = $this->queryBus->handle(new GetCartForViewing((int) $r['id_cart']));
            $r['total'] = $this->locale->formatPrice(
                $cartForViewing->getCartSummary()['total'],
                $this->contextCurrencyIsoCode
            );
            $modifiedRecord[] = $r;
        }

        return new RecordCollection($modifiedRecord);
    }
}
