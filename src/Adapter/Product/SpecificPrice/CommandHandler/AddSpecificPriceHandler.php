<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\CommandHandler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\AddSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\CommandHandler\AddSpecificPriceHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\SpecificPriceId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShopException;
use SpecificPrice;

/**
 * Handles AddProductSpecificPriceCommand using legacy object model
 */
#[AsCommandHandler]
class AddSpecificPriceHandler implements AddSpecificPriceHandlerInterface
{
    /**
     * @var SpecificPriceRepository
     */
    private $specificPriceRepository;

    /**
     * @param SpecificPriceRepository $specificPriceRepository
     */
    public function __construct(SpecificPriceRepository $specificPriceRepository)
    {
        $this->specificPriceRepository = $specificPriceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddSpecificPriceCommand $command): SpecificPriceId
    {
        $specificPrice = $this->createSpecificPriceFromCommand($command);

        return $this->specificPriceRepository->add($specificPrice);
    }

    /**
     * Creates legacy SpecificPrice object from command
     *
     * @param AddSpecificPriceCommand $command
     *
     * @return SpecificPrice
     *
     * @throws PrestaShopException
     * @throws SpecificPriceConstraintException
     */
    private function createSpecificPriceFromCommand(AddSpecificPriceCommand $command): SpecificPrice
    {
        $specificPrice = new SpecificPrice();

        $specificPrice->id_product = $command->getProductId()->getValue();
        $specificPrice->reduction_type = $command->getReduction()->getType();

        $reductionValue = $command->getReduction()->getValue();
        // VO stores percent expressed based on 100, while the DB stored the float value (VO: 57.5 - DB: 0.575)
        if ($command->getReduction()->getType() === Reduction::TYPE_PERCENTAGE) {
            $reductionValue = $reductionValue->dividedBy(new DecimalNumber('100'));
        }
        $specificPrice->reduction = (string) $reductionValue;

        $specificPrice->reduction_tax = $command->includesTax();
        $specificPrice->price = (string) $command->getFixedPrice()->getValue();
        $specificPrice->from_quantity = $command->getFromQuantity();
        $specificPrice->id_shop = $command->getShopId()->getValue();
        $specificPrice->id_product_attribute = $command->getCombinationId()->getValue();
        $specificPrice->id_currency = $command->getCurrencyId()->getValue();
        $specificPrice->id_country = $command->getCountryId()->getValue();
        $specificPrice->id_group = $command->getGroupId()->getValue();
        $specificPrice->id_customer = $command->getCustomerId();
        $specificPrice->from = $command->getDateTimeFrom()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
        $specificPrice->to = $command->getDateTimeTo()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);

        return $specificPrice;
    }
}
