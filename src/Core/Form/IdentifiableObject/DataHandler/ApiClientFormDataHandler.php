<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\AddApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\EditApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\CreatedApiClient;

class ApiClientFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private CommandBusInterface $commandBus
    ) {
    }

    public function create(array $data)
    {
        /** @var CreatedApiClient $createdApiClient */
        $createdApiClient = $this->commandBus->handle(new AddApiClientCommand(
            $data['client_name'],
            $data['client_id'],
            (bool) $data['enabled'],
            $data['description'],
            (int) $data['lifetime'],
            $data['scopes'],
        ));

        return $createdApiClient;
    }

    public function update($id, array $data)
    {
        $command = new EditApiClientCommand((int) $id);
        $command
            ->setClientName($data['client_name'])
            ->setClientId($data['client_id'])
            ->setDescription($data['description'])
            ->setEnabled((bool) $data['enabled'])
            ->setScopes($data['scopes'])
            ->setLifetime((int) $data['lifetime'])
        ;

        $this->commandBus->handle($command);
    }
}
