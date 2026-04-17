<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\Exception;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Exception is thrown when language is not found
 */
class LanguageNotFoundException extends LanguageException
{
    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @param LanguageId $languageId
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(LanguageId $languageId, $message = '', $code = 0, $previous = null)
    {
        $this->languageId = $languageId;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }
}
