<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\AddWebserviceKeyCommand;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\EditWebserviceKeyCommand;
use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\WebserviceKeyId;

/**
 * Creates/updates webservice key with submited form data
 */
final class WebserviceKeyFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @param CommandBusInterface $commandBus
     * @param int $contextShopId
     */
    public function __construct(CommandBusInterface $commandBus, $contextShopId)
    {
        $this->commandBus = $commandBus;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        if (!isset($data['shop_association'])) {
            $data['shop_association'] = [(int) $this->contextShopId];
        }

        /** @var WebserviceKeyId $webserviceKeyId */
        $webserviceKeyId = $this->commandBus->handle(new AddWebserviceKeyCommand(
            $data['key'],
            $data['description'],
            $data['status'],
            $data['permissions'],
            $data['shop_association']
        ));

        return $webserviceKeyId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($weserviceKeyId, array $data)
    {
        $editCommand = new EditWebserviceKeyCommand($weserviceKeyId);
        $editCommand
            ->setKey($data['key'])
            ->setDescription($data['description'])
            ->setStatus($data['status'])
            ->setPermissions($data['permissions'])
        ;

        if (isset($data['shop_association'])) {
            $editCommand->setShopAssociation($data['shop_association']);
        }

        $this->commandBus->handle($editCommand);
    }
}
