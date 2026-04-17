<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Exception;

use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Throwable;

/**
 * Thrown when an invalid key is found in a legacy translation file
 */
class InvalidLegacyTranslationKeyException extends CoreException
{
    /**
     * @var string The invalid key
     */
    private $key;

    public function __construct(string $missingElement, string $key, $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Invalid key in legacy translation file: "%s" (missing %s)', $key, $missingElement);
        $this->key = $key;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
