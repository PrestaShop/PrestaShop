<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\CreatedApiClient;
use PrestaShop\PrestaShop\Core\Domain\Tag\Command\AddTagCommand;
use PrestaShop\PrestaShop\Core\Domain\Tag\Command\EditTagCommand;

class TagFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private CommandBusInterface $commandBus
    ) {
    }

    public function create(array $data)
    {
        /** @var CreatedApiClient $createdApiClient */
        $createdApiClient = $this->commandBus->handle(new AddTagCommand(
            $data['name'],
            (int) $data['language'],
            $this->getProductIds($data['products']),
        ));

        return $createdApiClient;
    }

    public function update($id, array $data)
    {
        $command = (new EditTagCommand($id))
            ->setName($data['name'])
            ->setLanguageId((int) $data['language'])
            ->setProductIds($this->getProductIds($data['products']))
        ;

        $this->commandBus->handle($command);
    }

    protected function getProductIds(array $products): array
    {
        return array_map(function (array $product): int {
            return (int) $product['id'];
        }, $products);
    }
}
