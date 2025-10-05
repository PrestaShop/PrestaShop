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

namespace PrestaShop\PrestaShop\Core\Domain\Language\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;

/**
 * Stores Locale tag value (e.g. en-US)
 */
class Locale
{
    /**
     * Regexp to validate Locale
     */
    public const LOCALE_REGEXP = '^[a-z]{2}-[A-Z]{2}$';

    /**
     * @var string
     */
    private $locale;

    /**
     * @param string $locale
     *
     * @throws LanguageConstraintException
     */
    public function __construct($locale)
    {
        $this->assertIsLocale($locale);

        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @throws LanguageConstraintException
     */
    private function assertIsLocale($locale)
    {
        if (!is_string($locale) || empty($locale) || !preg_match(sprintf('/%s/', static::LOCALE_REGEXP), $locale)) {
            throw new LanguageConstraintException(sprintf('Invalid Locale %s provided', var_export($locale, true)), LanguageConstraintException::INVALID_LOCALE);
        }
    }
}
