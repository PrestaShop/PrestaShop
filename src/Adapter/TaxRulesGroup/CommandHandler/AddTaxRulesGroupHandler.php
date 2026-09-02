<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\AbstractTaxRulesGroupHandler;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\AddTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler\AddTaxRulesGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use TaxRulesGroup;

/**
 * Handles tax rules group addition
 */
#[AsCommandHandler]
class AddTaxRulesGroupHandler extends AbstractTaxRulesGroupHandler implements AddTaxRulesGroupHandlerInterface
{
    /**
     * @var TaxRulesGroupRepository
     */
    protected $taxRulesGroupRepository;

    /**
     * @param TaxRulesGroupRepository $taxRulesGroupRepository
     */
    public function __construct(TaxRulesGroupRepository $taxRulesGroupRepository)
    {
        $this->taxRulesGroupRepository = $taxRulesGroupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddTaxRulesGroupCommand $command): TaxRulesGroupId
    {
        $taxRulesGroup = new TaxRulesGroup();
        $taxRulesGroup->name = $command->getName();
        $taxRulesGroup->active = $command->isEnabled();

        $shopIds = [];
        foreach ($command->getShopAssociation() as $shopId) {
            $shopIds[] = new ShopId($shopId);
        }

        return $this->taxRulesGroupRepository->add($taxRulesGroup, $shopIds);
    }
}
