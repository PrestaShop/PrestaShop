<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
