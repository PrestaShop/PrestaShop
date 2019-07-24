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
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\AddContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\EditContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

/**
 * Class ContactFormDataHandler is responsible for handling create and update of contact form.
 */
final class ContactFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     *
     * @throws DomainException
     */
    public function create(array $data)
    {
        $addContactCommand = (new AddContactCommand($data['title'], $data['is_messages_saving_enabled']))
            ->setLocalisedDescription($data['description'])
            ->setShopAssociation(is_array($data['shop_association']) ? $data['shop_association'] : [])
        ;

        if ($data['email']) {
            $addContactCommand->setEmail($data['email']);
        }

        /** @var ContactId $result */
        $result = $this->commandBus->handle($addContactCommand);

        return $result->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws DomainException
     */
    public function update($contactId, array $data)
    {
        $editContactCommand = (new EditContactCommand((int) $contactId))
            ->setLocalisedTitles($data['title'])
            ->setIsMessagesSavingEnabled($data['is_messages_saving_enabled'])
            ->setLocalisedDescription($data['description'])
            ->setShopAssociation(is_array($data['shop_association']) ? $data['shop_association'] : [])
        ;

        if ($data['email']) {
            $editContactCommand->setEmail($data['email']);
        }

        $this->commandBus->handle($editContactCommand);
    }
}
