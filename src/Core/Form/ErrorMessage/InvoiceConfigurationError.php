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

namespace PrestaShop\PrestaShop\Core\Form\ErrorMessage;

class InvoiceConfigurationError implements ConfigurationErrorInterface
{
    public const ERROR_INVALID_DATE_FROM = 'invalid_date_from';
    public const ERROR_INVALID_DATE_TO = 'invalid_date_to';
    public const ERROR_NO_INVOICES_FOUND = 'no_invoices_found';
    public const ERROR_NO_INVOICES_FOUND_FOR_STATUS = 'no_invoices_found_for_status';
    public const ERROR_INCORRECT_INVOICE_NUMBER = 'incorrect_invoice_number';
    public const ERROR_NO_ORDER_STATE_SELECTED = 'no_order_state_selected';

    /**
     * @var string
     */
    private $errorCode;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var int|null
     */
    private $languageId;

    /**
     * InvoiceConfigurationError constructor.
     *
     * @param string $errorCode
     * @param string $fieldName
     * @param int|null $languageId
     */
    public function __construct(string $errorCode, string $fieldName, ?int $languageId = null)
    {
        $this->errorCode = $errorCode;
        $this->fieldName = $fieldName;
        $this->languageId = $languageId;
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * @return int|null
     */
    public function getLanguageId(): ?int
    {
        return $this->languageId;
    }
}
