<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\ValueObject;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;

/**
 * Provides valid country zip code
 */
class CountryZipCodeFormat
{
    /**
     * @var string
     */
    private $zipCodeFormat;

    /**
     * @param string $zipCodeFormat
     */
    public function __construct(string $zipCodeFormat)
    {
        $this->assertIsValidZipCodeFormat($zipCodeFormat);
        $this->zipCodeFormat = $zipCodeFormat;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->zipCodeFormat;
    }

    /**
     * @param string $zipCodeFormat
     *
     * @throws CountryConstraintException
     */
    private function assertIsValidZipCodeFormat(string $zipCodeFormat): void
    {
        if (!preg_match(TypedRegex::TYPE_ZIP_CODE_FORMAT, $zipCodeFormat)) {
            throw new CountryConstraintException(sprintf('Invalid country zip code format: %s', $zipCodeFormat), CountryConstraintException::INVALID_ZIP_CODE);
        }
    }
}
