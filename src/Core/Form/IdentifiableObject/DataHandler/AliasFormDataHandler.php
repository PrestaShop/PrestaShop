<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
