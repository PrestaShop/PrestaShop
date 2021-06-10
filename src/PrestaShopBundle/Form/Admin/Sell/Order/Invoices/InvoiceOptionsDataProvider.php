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

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Exception\TypeException;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShopBundle\Form\Exception\DataProviderException;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataError;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataErrorCollection;

/**
 * Class is responsible of managing the data manipulated using invoice options form
 * in "Sell > Orders > Invoices" page.
 */
final class InvoiceOptionsDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $invoiceOptionsConfiguration;

    /**
     * @var int
     */
    private $nextInvoiceNumber;

    /**
     * @param DataConfigurationInterface $invoiceOptionsConfiguration
     * @param int $nextInvoiceNumber next available invoice number
     */
    public function __construct(
        DataConfigurationInterface $invoiceOptionsConfiguration,
        $nextInvoiceNumber
    ) {
        $this->invoiceOptionsConfiguration = $invoiceOptionsConfiguration;
        $this->nextInvoiceNumber = $nextInvoiceNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->invoiceOptionsConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        $this->validate($data);

        return $this->invoiceOptionsConfiguration->updateConfiguration($data);
    }

    /**
     * Perform validations on form data.
     *
     * @param array $data
     *
     * @return void
     *
     * @throws TypeException|DataProviderException
     */
    private function validate(array $data): void
    {
        $errorCollection = new InvalidConfigurationDataErrorCollection();

        if (isset($data[InvoiceOptionsType::FIELD_INVOICE_NUMBER])) {
            $invoiceNumber = $data[InvoiceOptionsType::FIELD_INVOICE_NUMBER];
            if ($invoiceNumber > 0 && $invoiceNumber <= $this->nextInvoiceNumber) {
                $errorCollection->add(
                    new InvalidConfigurationDataError(
                        InvalidConfigurationDataError::ERROR_INCORRECT_INVOICE_NUMBER,
                        InvoiceOptionsType::FIELD_INVOICE_NUMBER
                    )
                );
            }
        }

        if (isset($data[InvoiceOptionsType::FIELD_INVOICE_PREFIX]) && is_array($data[InvoiceOptionsType::FIELD_INVOICE_PREFIX])) {
            $this->validateContainsNoTags($data[InvoiceOptionsType::FIELD_INVOICE_PREFIX], InvoiceOptionsType::FIELD_INVOICE_PREFIX, $errorCollection);
        }

        if (isset($data[InvoiceOptionsType::FIELD_LEGAL_FREE_TEXT]) && is_array($data[InvoiceOptionsType::FIELD_LEGAL_FREE_TEXT])) {
            $this->validateContainsNoTags($data[InvoiceOptionsType::FIELD_LEGAL_FREE_TEXT], InvoiceOptionsType::FIELD_LEGAL_FREE_TEXT, $errorCollection);
        }

        if (isset($data[InvoiceOptionsType::FIELD_FOOTER_TEXT]) && is_array($data[InvoiceOptionsType::FIELD_FOOTER_TEXT])) {
            $this->validateContainsNoTags($data[InvoiceOptionsType::FIELD_FOOTER_TEXT], InvoiceOptionsType::FIELD_FOOTER_TEXT, $errorCollection);
        }

        if (!$errorCollection->isEmpty()) {
            throw new DataProviderException('Invalid invoice options form data', 0, null, $errorCollection);
        }
    }

    /**
     * If any of values of multilang field contain html tags and if they do add error.
     *
     * @param array $data
     * @param string $key
     * @param InvalidConfigurationDataErrorCollection $errorCollection
     *
     * @throws TypeException
     */
    private function validateContainsNoTags(array $data, string $key, InvalidConfigurationDataErrorCollection $errorCollection): void
    {
        foreach ($data as $languageId => $value) {
            if ($value !== null && $value !== strip_tags($value)) {
                $errorCollection->add(
                    new InvalidConfigurationDataError(
                        InvalidConfigurationDataError::ERROR_CONTAINS_HTML_TAGS,
                        $key,
                        $languageId
                    )
                );
            }
        }
    }
}
