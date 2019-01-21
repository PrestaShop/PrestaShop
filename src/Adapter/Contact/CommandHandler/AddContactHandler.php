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
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\AddContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\CommandHandler\AddContactHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\CannotAddContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShopException;

/**
 * Class AddContactHandler is used for adding contact data.
 */
final class AddContactHandler implements AddContactHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ContactException
     */
    public function handle(AddContactCommand $command)
    {
        //todo: some validations missing
        try {
            $entity = new Contact();
            $entity->name = $command->getLocalisedTitles();
            $entity->customer_service = $command->isMessageSavingEnabled();

            if (null !== $command->getEmail()) {
                $entity->email = $command->getEmail();
            }

            if (null !== $command->getLocalisedDescription()) {
                $entity->description = $command->getLocalisedDescription();
            }

            if (null !== $command->getShopAssociation()) {
                //todo: legacy object model use for entity shop association
            }

            if (false === $entity->add()) {
                throw new CannotAddContactException(
                    'Unable to add contact'
                );
            }
        } catch (PrestaShopException $exception) {
            throw new ContactException(
                'An unexpected error occurred when adding contact',
                0,
                $exception
            );
        }
    }
}
