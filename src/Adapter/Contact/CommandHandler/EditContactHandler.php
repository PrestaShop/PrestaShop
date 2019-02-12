<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Contact\CommandHandler;

use Contact;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\EditContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\CommandHandler\EditContactHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\CannotUpdateContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;
use PrestaShopException;

/**
 * Class EditContactHandler is responsible for editing contact data.
 */
final class EditContactHandler extends AbstractObjectModelHandler implements EditContactHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ContactException
     */
    public function handle(EditContactCommand $command)
    {
        try {
            $entity = new Contact($command->getContactId()->getValue());

            if (0 >= $entity->id) {
                throw new ContactNotFoundException(
                    sprintf(
                        'Contact object with id %s was not found',
                        var_export($command->getContactId()->getValue(), true)
                    )
                );
            }

            if (null !== $command->getLocalisedTitles()) {
                $entity->name = $command->getLocalisedTitles();
            }

            if (null !== $command->getLocalisedDescription()) {
                $entity->description = $command->getLocalisedDescription();
            }

            if (null !== $command->getEmail()) {
                $entity->email = $command->getEmail()->getValue();
            }

            if (null !== $command->isMessagesSavingEnabled()) {
                $entity->customer_service = $command->isMessagesSavingEnabled();
            }

            if (false === $entity->update()) {
                throw new CannotUpdateContactException(
                    sprintf(
                        'Unable to update contact object with id %s',
                        $command->getContactId()->getValue()
                    )
                );
            }

            if (null !== $command->getShopAssociation()) {
                $this->associateWithShops($entity, $command->getShopAssociation());
            }

        } catch (PrestaShopException $e) {
            throw new ContactException(
                sprintf(
                    'An unexpected error occurred when retrieving contact with id %s',
                    var_export($command->getContactId()->getValue(), true)
                ),
                0,
                $e
            );
        }

        return new ContactId((int) $entity->id);
    }
}
