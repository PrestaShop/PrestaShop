<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step;

use DateTime;
use DateTimeInterface;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\NoCountryId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\NoCurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\NoGroupId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\NoCustomerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\AddSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\EditSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\NoShopId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;

/**
 * Legacy "basic reduction": one specific price rule, all currencies/
 * countries/groups, from quantity 1. A row carrying BOTH reduction kinds
 * is ambiguous: both are dropped (the validator already warned).
 *
 * Re-importing a row is an UPDATE, not an error: the repository rejects a
 * duplicate rule (SpecificPriceConstraintException::NOT_UNIQUE_PER_PRODUCT),
 * and because that lands in the row catch-all it would fail the whole row
 * and get its accessories dropped in the association phase. So the existing
 * rule is looked up first and edited when found.
 *
 * KNOWN LIMITATION: the lookup is keyed on the rule's dates, so a row that
 * only changes reduction_from/reduction_to does not match and adds a second
 * rule. Defining what identifies "the import's basic reduction" independently
 * of its dates is a separate discussion (see PLAN.md).
 */
class SpecificPriceStep extends AbstractProductRowStep
{
    public function __construct(
        ValueParser $valueParser,
        protected readonly SpecificPriceRepository $specificPriceRepository,
        protected readonly CommandBusInterface $commandBus,
    ) {
        parent::__construct($valueParser);
    }

    public function supports(array $row): bool
    {
        return $this->hasValue($row, 'reduction_price') || $this->hasValue($row, 'reduction_percent');
    }

    public function apply(array $row, int $rowIndex, int $productId, bool $isCreation, int $languageId, ImportRunContext $context): array
    {
        if ($this->hasValue($row, 'reduction_price') && $this->hasValue($row, 'reduction_percent')) {
            return [];
        }

        $reductionPrice = $this->valueParser->parseDecimal($row['reduction_price'] ?? '');
        $reductionPercent = $this->valueParser->parseDecimal($row['reduction_percent'] ?? '');

        if (null === $reductionPrice && null === $reductionPercent) {
            return [];
        }

        $reductionType = null !== $reductionPrice ? Reduction::TYPE_AMOUNT : Reduction::TYPE_PERCENTAGE;
        $reductionValue = null !== $reductionPrice ? $reductionPrice : $reductionPercent;

        $from = $this->valueParser->parseDate($row['reduction_from'] ?? '');
        $to = $this->valueParser->parseDate($row['reduction_to'] ?? '');
        $dateTimeFrom = null !== $from ? DateTime::createFromImmutable($from) : new NullDateTime();
        $dateTimeTo = null !== $to ? DateTime::createFromImmutable($to) : new NullDateTime();

        $existingSpecificPriceId = $this->findExistingBasicSpecificPriceId($productId, $dateTimeFrom, $dateTimeTo);
        if (null !== $existingSpecificPriceId) {
            $editCommand = new EditSpecificPriceCommand($existingSpecificPriceId);
            $editCommand->setReduction($reductionType, (string) $reductionValue);
            $this->commandBus->handle($editCommand);

            return [];
        }

        $this->commandBus->handle(new AddSpecificPriceCommand(
            $productId,
            $reductionType,
            (string) $reductionValue,
            true,
            '-1',
            1,
            $dateTimeFrom,
            $dateTimeTo
        ));

        return [];
    }

    /**
     * The uniqueness key AddSpecificPriceCommand produces: no combination, no
     * shop, no group, no country, no currency, no customer, from quantity 1.
     * Mirroring it here is what makes the re-import idempotent.
     */
    protected function findExistingBasicSpecificPriceId(
        int $productId,
        DateTimeInterface $dateTimeFrom,
        DateTimeInterface $dateTimeTo
    ): ?int {
        $existingSpecificPriceId = $this->specificPriceRepository->findExisting(
            $productId,
            NoCombinationId::NO_COMBINATION_ID,
            NoShopId::NO_SHOP_ID,
            NoGroupId::NO_GROUP_ID,
            NoCountryId::NO_COUNTRY_ID_VALUE,
            NoCurrencyId::NO_CURRENCY_ID,
            NoCustomerId::NO_CUSTOMER_ID_VALUE,
            1,
            $dateTimeFrom->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
            $dateTimeTo->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT)
        );

        return $existingSpecificPriceId?->getValue();
    }
}
