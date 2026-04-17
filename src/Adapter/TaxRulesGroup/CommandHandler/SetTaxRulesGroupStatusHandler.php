<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\AbstractTaxRulesGroupHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\SetTaxRulesGroupStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler\ToggleTaxRulesGroupStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotUpdateTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupNotFoundException;

/**
 * Handles tax rules group status toggling
 */
#[AsCommandHandler]
final class SetTaxRulesGroupStatusHandler extends AbstractTaxRulesGroupHandler implements ToggleTaxRulesGroupStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotUpdateTaxRulesGroupException
     * @throws TaxRulesGroupException
     * @throws TaxRulesGroupNotFoundException
     */
    public function handle(SetTaxRulesGroupStatusCommand $command): void
    {
        $taxRulesGroup = $this->getTaxRulesGroup($command->getTaxRulesGroupId());

        if (!$this->setTaxRulesGroupStatus($taxRulesGroup, $command->getExpectedStatus())) {
            throw new CannotUpdateTaxRulesGroupException(sprintf('Unable to toggle tax rules group status with id "%s"', $taxRulesGroup->id), CannotUpdateTaxRulesGroupException::FAILED_TOGGLE_STATUS);
        }
    }
}
