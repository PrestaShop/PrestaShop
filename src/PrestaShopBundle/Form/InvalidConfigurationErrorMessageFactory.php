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

declare(strict_types=1);

namespace PrestaShopBundle\Form;

use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShopBundle\Controller\Exception\FieldNotFoundException;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\GeneralDataProvider;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataError;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataErrorCollection;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

class InvalidConfigurationErrorMessageFactory
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var LangRepository
     */
    private $langRepository;

    public function __construct(
        TranslatorInterface $translator,
        LangRepository $langRepository
    ) {
        $this->translator = $translator;
        $this->langRepository = $langRepository;
    }

    /**
     * @param InvalidConfigurationDataErrorCollection $errors
     * @param FormInterface $form
     *
     * @return array
     *
     * @throws FieldNotFoundException
     */
    public function getErrorMessages(InvalidConfigurationDataErrorCollection $errors, FormInterface $form): array
    {
        $messages = [];

        foreach ($errors as $error) {
            $messages[] = $this->getErrorMessageForConfigurationError($error, $this->getLabel($form, $error->getFieldName()));
        }

        return $messages;
    }

    /**
     * @param InvalidConfigurationDataError $error
     * @param string $label
     *
     * @return string
     */
    protected function getErrorMessageForConfigurationError(InvalidConfigurationDataError $error, string $label): string
    {
        switch ($error->getErrorCode()) {
            case InvalidConfigurationDataError::ERROR_INVALID_DATE_TO:
            case InvalidConfigurationDataError::ERROR_INVALID_DATE_FROM:
                return $this->translator->trans(
                    'Invalid "%s" date.',
                    [
                        $label,
                    ],
                    'Admin.Orderscustomers.Notification'
                );
            case InvalidConfigurationDataError::ERROR_NO_INVOICES_FOUND:
                return $this->translator->trans(
                    'No invoice has been found for this period.',
                    [],
                    'Admin.Orderscustomers.Notification'
                );
            case InvalidConfigurationDataError::ERROR_NO_ORDER_STATE_SELECTED:
                return $this->translator->trans(
                    'You must select at least one order status.',
                    [],
                    'Admin.Orderscustomers.Notification'
                );
            case InvalidConfigurationDataError::ERROR_NO_INVOICES_FOUND_FOR_STATUS:
                return $this->translator->trans(
                    'No invoice has been found for this status.',
                    [],
                    'Admin.Orderscustomers.Notification'
                );
            case InvalidConfigurationDataError::ERROR_INCORRECT_INVOICE_NUMBER:
                return $this->translator->trans(
                    'Invoice number must be greater than the last invoice number, or 0 if you want to keep the current number.',
                    [],
                    'Admin.Orderscustomers.Notification'
                );
            case InvalidConfigurationDataError::ERROR_CONTAINS_HTML_TAGS:
                if ($error->getLanguageId()) {
                    $lang = $this->langRepository->findOneBy(['id' => $error->getLanguageId()]);

                    return $this->translator->trans(
                        'The "%s" field in %s is invalid. HTML tags are not allowed.',
                        [
                            $label,
                            $lang->getName(),
                        ],
                        'Admin.Orderscustomers.Notification'
                    );
                }

                return $this->translator->trans(
                    'The "%s" field is invalid. HTML tags are not allowed.',
                    [
                        $label,
                    ],
                    'Admin.Notifications.Error'
                );
            case InvalidConfigurationDataError::ERROR_NOT_NUMERIC_OR_LOWER_THAN_ZERO:
                return $this->translator->trans(
                    '%s is invalid. Please enter an integer greater than or equal to 0.',
                    [
                        $label,
                    ],
                    'Admin.Notifications.Error'
                );
            case InvalidConfigurationDataError::ERROR_COOKIE_LIFETIME_MAX_VALUE_EXCEEDED:
                return $this->translator->trans(
                    '%s is invalid. Please enter an integer lower than %s.',
                    [
                        $label,
                        GeneralDataProvider::MAX_COOKIE_VALUE,
                    ],
                    'Admin.Notifications.Error'
                );
            case InvalidConfigurationDataError::ERROR_COOKIE_SAMESITE_NONE:
                return $this->translator->trans(
                    'The SameSite=None is only available in secure mode.',
                    [],
                    'Admin.Advparameters.Notification'
                );
        }

        return $this->translator->trans(
            '%s is invalid.',
            [
                $label,
            ],
            'Admin.Notifications.Error'
        );
    }

    protected function getLabel(FormInterface $form, string $fieldName): string
    {
        $view = $form->createView();
        foreach ($view->children as $child) {
            if ($fieldName === $child->vars['name']) {
                if (!isset($child->vars['label'])) {
                    throw new FieldNotFoundException(
                        sprintf(
                            'Field %s doesn\'t have a label set in Form Type',
                            $fieldName
                        )
                    );
                }

                return $child->vars['label'];
            }
        }

        throw new FieldNotFoundException(
            sprintf(
                'Field name for field %s not found',
                $fieldName
            )
        );
    }
}
