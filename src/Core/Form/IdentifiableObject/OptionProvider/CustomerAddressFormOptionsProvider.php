<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetRequiredFieldsForAddress;

/**
 * Provide dynamic complex options to the address type (like preview data that depend
 * on address current data, or specific options for inputs that are deep in the form
 * structure).
 */
class CustomerAddressFormOptionsProvider implements FormOptionsProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(CommandBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(int $id, array $data): array
    {
        return $this->getRequiredFields();
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultOptions(array $data): array
    {
        return $this->getRequiredFields();
    }

    private function getRequiredFields(): array
    {
        return ['requiredFields' => $this->queryBus->handle(new GetRequiredFieldsForAddress())];
    }
}
