<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\AddTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\EditTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;

/**
 * Handles submitted tax form data
 */
final class TaxFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

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
     */
    public function create(array $data)
    {
        $command = new AddTaxCommand(
            $data['name'],
            (float) $data['rate'],
            (bool) $data['is_enabled']
        );

        /** @var TaxId $taxId */
        $taxId = $this->commandBus->handle($command);

        return $taxId->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws TaxException
     */
    public function update($id, array $data)
    {
        $command = (new EditTaxCommand($id))
            ->setLocalizedNames($data['name'])
            ->setRate((float) $data['rate'])
            ->setEnabled((bool) $data['is_enabled'])
        ;

        $this->commandBus->handle($command);
    }
}
