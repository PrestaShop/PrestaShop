<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\AddTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\EditTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * Handles submitted tax form data
 */
class TaxRulesGroupFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * Create object from form data.
     *
     * @param array $data
     *
     * @return mixed
     *
     * @throws TaxRulesGroupConstraintException
     */
    public function create(array $data)
    {
        $command = new AddTaxRulesGroupCommand(
            $data['name'],
            (bool) $data['is_enabled']
        );
        if (isset($data['shop_association'])) {
            $command->setShopAssociation(is_array($data['shop_association']) ? $data['shop_association'] : []);
        }

        /** @var TaxRulesGroupId $taxRulesGroupId */
        $taxRulesGroupId = $this->commandBus->handle($command);

        return $taxRulesGroupId->getValue();
    }

    /**
     * {@inheritDoc}
     *
     * @throws TaxRulesGroupConstraintException
     */
    public function update($id, array $data)
    {
        $command = (new EditTaxRulesGroupCommand($id))
            ->setName($data['name'])
            ->setEnabled((bool) $data['is_enabled'])
            ->setShopAssociation(is_array($data['shop_association']) ? $data['shop_association'] : [])
        ;

        $this->commandBus->handle($command);
    }
}
