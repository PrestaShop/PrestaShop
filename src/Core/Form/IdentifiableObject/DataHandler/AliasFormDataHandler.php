<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\AddSearchTermAliasesCommand;
use PrestaShop\PrestaShop\Core\Domain\Alias\Command\UpdateSearchTermAliasesCommand;

class AliasFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function create(array $data)
    {
        $searchTerm = $data['search'];
        $aliases = $this->formatAliases($data['aliases']);

        return $this->commandBus->handle(new AddSearchTermAliasesCommand($aliases, $searchTerm));
    }

    public function update($id, array $data)
    {
        $searchTerm = $data['search'];
        $aliases = $this->formatAliases($data['aliases']);

        return $this->commandBus->handle(new UpdateSearchTermAliasesCommand((string) $id, $aliases, $searchTerm));
    }

    protected function formatAliases(array $aliases)
    {
        $formattedAliases = [];

        foreach ($aliases as $alias) {
            if (empty($alias['alias'])) {
                continue;
            }
            $formattedAliases[] = [
                'alias' => trim($alias['alias']),
                'active' => $alias['active'],
            ];
        }

        return $formattedAliases;
    }
}
