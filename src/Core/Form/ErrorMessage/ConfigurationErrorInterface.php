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

namespace PrestaShop\PrestaShop\Core\Form\ErrorMessage;

interface ConfigurationErrorInterface
{
    /**
     * Constants for common errors
     */
    public const ERROR_NOT_NUMERIC_OR_LOWER_THAN_ZERO = 'error_not_numeric_or_lower_than_zero';
    public const ERROR_CONTAINS_HTML_TAGS = 'contains_html_tags';

    /**
     * @return string
     */
    public function getErrorCode(): string;

    /**
     * @return string
     */
    public function getFieldName(): string;

    /**
     * @return int|null
     */
    public function getLanguageId(): ?int;
}
