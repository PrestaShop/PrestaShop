<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;

/**
 * Stores language's two-letter (639-1) ISO code
 */
class IsoCode
{
    /**
     * @var string ISO Code validation pattern
     */
    public const PATTERN = '/^[a-zA-Z]{2,3}$/';

    /**
     * @var string
     */
    private $isoCode;

    /**
     * @param string $isoCode
     */
    public function __construct($isoCode)
    {
        $this->assertIsIsoCode($isoCode);

        $this->isoCode = strtolower($isoCode);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->isoCode;
    }

    /**
     * @param string $isoCode
     *
     * @throws LanguageException
     */
    private function assertIsIsoCode($isoCode)
    {
        if (!is_string($isoCode) || !preg_match('/^[a-zA-Z]{2,3}$/', $isoCode)) {
            throw new LanguageConstraintException(sprintf('Invalid language ISO code %s supplied', var_export($isoCode, true)), LanguageConstraintException::INVALID_ISO_CODE);
        }
    }
}
