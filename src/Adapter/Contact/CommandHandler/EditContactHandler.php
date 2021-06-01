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

namespace PrestaShop\PrestaShop\Adapter\Contact\CommandHandler;

use Contact;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\EditContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\CommandHandler\EditContactHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\CannotUpdateContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactNotFoundException;
use PrestaShopDatabaseException;
use PrestaShopException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class EditContactHandler is responsible for editing contact data.
 *
 * @internal
 */
final class EditContactHandler extends AbstractObjectModelHandler implements EditContactHandlerInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ContactException
     */
    public function handle(EditContactCommand $command)
    {
        try {
            $entity = $this->getContactEntityIfFound($command->getContactId()->getValue());

            if (null !== $command->getLocalisedTitles()) {
                $this->assertLocalisedTitleContainsDefaultLanguage($command->getLocalisedTitles());
                $entity->name = $command->getLocalisedTitles();
            }

            if (null !== $command->getLocalisedDescription()) {
                $this->assertDescriptionContainsCleanHtmlValues($command->getLocalisedDescription());
                $entity->description = $command->getLocalisedDescription();
            }

            if (null !== $command->getEmail()) {
                $entity->email = $command->getEmail()->getValue();
            }

            if (null !== $command->isMessagesSavingEnabled()) {
                $entity->customer_service = $command->isMessagesSavingEnabled();
            }

            if (false === $entity->update()) {
                throw new CannotUpdateContactException(sprintf('Unable to update contact object with id %s', $command->getContactId()->getValue()));
            }

            if (null !== $command->getShopAssociation()) {
                $this->associateWithShops($entity, $command->getShopAssociation());
            }
        } catch (PrestaShopException $e) {
            throw new ContactException(sprintf('An unexpected error occurred when retrieving contact with id %s', var_export($command->getContactId()->getValue(), true)), 0, $e);
        }
    }

    /**
     * Validates that values does not contain script tags or javascript events.
     *
     * @param array $localisedDescriptions
     *
     * @throws ContactConstraintException
     */
    private function assertDescriptionContainsCleanHtmlValues(array $localisedDescriptions)
    {
        foreach ($localisedDescriptions as $description) {
            $errors = $this->validator->validate($description, new CleanHtml());

            if (0 !== count($errors)) {
                throw new ContactConstraintException(sprintf('Given description "%s" contains javascript events or script tags', $description), ContactConstraintException::INVALID_DESCRIPTION);
            }
        }
    }

    /**
     * Checks if the localised titles array contains value for the default language.
     *
     * @param array $localisedTitle
     *
     * @throws ContactConstraintException
     */
    private function assertLocalisedTitleContainsDefaultLanguage(array $localisedTitle)
    {
        $errors = $this->validator->validate($localisedTitle, new DefaultLanguage());

        if (0 !== count($errors)) {
            throw new ContactConstraintException('Title field is not found for default language', ContactConstraintException::MISSING_TITLE_FOR_DEFAULT_LANGUAGE);
        }
    }

    /**
     * Gets contact entity.
     *
     * @param int $contactId
     *
     * @return Contact
     *
     * @throws ContactNotFoundException
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    private function getContactEntityIfFound($contactId)
    {
        $entity = new Contact($contactId);

        if (0 >= $entity->id) {
            throw new ContactNotFoundException(sprintf('Contact object with id %s was not found', var_export($contactId, true)));
        }

        return $entity;
    }
}
