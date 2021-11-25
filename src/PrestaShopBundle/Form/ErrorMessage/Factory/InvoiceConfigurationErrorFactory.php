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

namespace PrestaShopBundle\Form\ErrorMessage\Factory;

use PrestaShop\PrestaShop\Core\Form\ErrorMessage\ConfigurationErrorInterface;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\Factory\ConfigurationErrorFactoryInterface;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\InvoiceConfigurationError;
use PrestaShopBundle\Entity\Repository\LangRepository;
use Symfony\Component\Translation\TranslatorInterface;

class InvoiceConfigurationErrorFactory implements ConfigurationErrorFactoryInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var LangRepository
     */
    protected $langRepository;

    public function __construct(
        TranslatorInterface $translator,
        LangRepository $langRepository
    ) {
        $this->translator = $translator;
        $this->langRepository = $langRepository;
    }

    /**
     * @param ConfigurationErrorInterface $error
     * @param string $label
     *
     * @return string|null
     */
    public function getErrorMessageForConfigurationError(ConfigurationErrorInterface $error, string $label): ?string
    {
        switch ($error->getErrorCode()) {
            case InvoiceConfigurationError::ERROR_INVALID_DATE_TO:
            case InvoiceConfigurationError::ERROR_INVALID_DATE_FROM:
                return $this->translator->trans(
                    'Invalid "%s" date.',
                    [
                        $label,
                    ],
                    'Admin.Orderscustomers.Notification'
                );
            case InvoiceConfigurationError::ERROR_NO_INVOICES_FOUND:
                return $this->translator->trans(
                    'No invoice has been found for this period.',
                    [],
                    'Admin.Orderscustomers.Notification'
                );
            case InvoiceConfigurationError::ERROR_NO_ORDER_STATE_SELECTED:
                return $this->translator->trans(
                    'You must select at least one order status.',
                    [],
                    'Admin.Orderscustomers.Notification'
                );
            case InvoiceConfigurationError::ERROR_NO_INVOICES_FOUND_FOR_STATUS:
                return $this->translator->trans(
                    'No invoice has been found for this status.',
                    [],
                    'Admin.Orderscustomers.Notification'
                );
            case InvoiceConfigurationError::ERROR_INCORRECT_INVOICE_NUMBER:
                return $this->translator->trans(
                    'Invoice number must be greater than the last invoice number, or 0 if you want to keep the current number.',
                    [],
                    'Admin.Orderscustomers.Notification'
                );
        }

        return null;
    }
}
