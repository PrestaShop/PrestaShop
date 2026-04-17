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
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\EditTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler\EditTaxRulesGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotUpdateTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupException;

/**
 * Handles tax rules group edition
 */
#[AsCommandHandler]
class EditTaxRulesGroupHandler extends AbstractTaxRulesGroupHandler implements EditTaxRulesGroupHandlerInterface
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
     *
     * @throws CannotUpdateTaxRulesGroupException
     * @throws TaxRulesGroupException
     */
    public function handle(EditTaxRulesGroupCommand $command): void
    {
        $taxRulesGroup = $this->getTaxRulesGroup($command->getTaxRulesGroupId());

        $updatableProperties = [];
        if (null !== $command->getName()) {
            $taxRulesGroup->name = $command->getName();
            $updatableProperties[] = 'name';
        }
        if (null !== $command->isEnabled()) {
            $taxRulesGroup->active = $command->isEnabled();
            $updatableProperties[] = 'active';
        }

        $shopIds = [];
        foreach ($command->getShopAssociation() ?? [] as $shopId) {
            $shopIds[] = new ShopId($shopId);
        }

        $this->taxRulesGroupRepository->partialUpdate(
            $taxRulesGroup,
            $updatableProperties,
            $shopIds,
            CannotUpdateTaxRulesGroupException::FAILED_UPDATE_TAX_RULES_GROUP
        );
    }
}
