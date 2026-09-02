<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
