<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\Exception;

use Throwable;

/**
 * Thrown on failure to delete Webservice
 */
class CannotDeleteWebserviceException extends WebserviceException
{
    /**
     * @var array<int, array<string, array|string>>
     */
    private $errors;

    /**
     * @param array<int, array<string, array|string>> $errors
     * @param string $message
     * @param int $code
     * @param Throwable $previous
     */
    public function __construct(array $errors, $message = '', $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }

    /**
     * @return array<int, array<string, array|string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
