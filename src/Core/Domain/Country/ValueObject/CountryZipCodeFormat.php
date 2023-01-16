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

namespace PrestaShop\PrestaShop\Core\Domain\Country\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;

/**
 * Contains a valid zip code format for country
 */
class CountryZipCodeFormat
{
    /**
     * Zip code format regexp validation pattern
     */
    public const ZIP_CODE_PATTERN = '/^[NLCnlc 0-9-]+$/';

    /**
     * @var string
     */
    protected $zipCodeFormat;

    public function __construct(string $zipCodeFormat)
    {
        $this->assertIsValidZipCodeFormat($zipCodeFormat);
        $this->zipCodeFormat = $zipCodeFormat;
    }

    public function getValue(): string
    {
        return $this->zipCodeFormat;
    }

    /**
     * @param string $zipCodeFormat
     *
     * @throws CountryConstraintException
     */
    protected function assertIsValidZipCodeFormat(string $zipCodeFormat): void
    {
        if (!preg_match(self::ZIP_CODE_PATTERN, $zipCodeFormat)) {
            throw new CountryConstraintException(
                sprintf('Invalid country zip code format: %s', $zipCodeFormat),
                CountryConstraintException::INVALID_ZIP_CODE
            );
        }
    }
}
