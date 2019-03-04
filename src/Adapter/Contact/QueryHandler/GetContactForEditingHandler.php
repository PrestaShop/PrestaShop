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

namespace PrestaShop\PrestaShop\Adapter\Contact\QueryHandler;

use Contact;
use PrestaShop\PrestaShop\Core\Domain\Contact\DTO\EditableContact;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Query\GetContactForEditing;
use PrestaShop\PrestaShop\Core\Domain\Contact\QueryHandler\GetContactForEditingHandlerInterface;

/**
 * Class GetContactForEditingHandler is responsible for getting the data for contact edit page.
 */
final class GetContactForEditingHandler implements GetContactForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ContactException
     */
    public function handle(GetContactForEditing $query)
    {
        $contact = new Contact($query->getContactId()->getValue());

        if (0 >= $contact->id) {
            throw new ContactNotFoundException(
                sprintf(
                    'Contact object with id %s was not found',
                    var_export($query->getContactId()->getValue(), true)
                )
            );
        }

        return new EditableContact(
            $query->getContactId()->getValue(),
            $contact->name,
            $contact->email,
            $contact->customer_service,
            $contact->description
        );
    }
}
