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

namespace PrestaShop\PrestaShop\Adapter\Contact\QueryHandler;

use Contact;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Query\GetContactForEditing;
use PrestaShop\PrestaShop\Core\Domain\Contact\QueryHandler\GetContactForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Contact\QueryResult\EditableContact;
use PrestaShopException;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class GetContactForEditingHandler is responsible for getting the data for contact edit page.
 *
 * @internal
 */
#[AsQueryHandler]
final class GetContactForEditingHandler implements GetContactForEditingHandlerInterface
{
    /**
     * @var DataTransformerInterface
     */
    private $stringArrayToIntegerArrayDataTransformer;

    /**
     * @param DataTransformerInterface $stringArrayToIntegerArrayDataTransformer
     */
    public function __construct(DataTransformerInterface $stringArrayToIntegerArrayDataTransformer)
    {
        $this->stringArrayToIntegerArrayDataTransformer = $stringArrayToIntegerArrayDataTransformer;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ContactException
     */
    public function handle(GetContactForEditing $query)
    {
        try {
            $contact = new Contact($query->getContactId()->getValue());

            if (0 >= $contact->id) {
                throw new ContactNotFoundException(sprintf('Contact object with id %s was not found', var_export($query->getContactId()->getValue(), true)));
            }
            $editableContact = new EditableContact(
                $query->getContactId()->getValue(),
                $contact->name,
                $contact->email,
                (bool) $contact->customer_service,
                $contact->description,
                $this->stringArrayToIntegerArrayDataTransformer->reverseTransform($contact->getAssociatedShops())
            );
        } catch (PrestaShopException $e) {
            throw new ContactException(sprintf('An unexpected error occurred when retrieving contact with id %s', var_export($query->getContactId()->getValue(), true)), 0, $e);
        }

        return $editableContact;
    }
}
