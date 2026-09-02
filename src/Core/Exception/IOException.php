<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Exception;

use Throwable;

/**
 * Exception class thrown when a filesystem operation failure happens.
 */
class IOException extends CoreException
{
    private $path;

    /**
     * @param string $message
     * @param int $code
     * @param Throwable $previous
     * @param string|null $path
     */
    public function __construct($message = '', $code = 0, ?Throwable $previous = null, $path = null)
    {
        parent::__construct($message, $code, $previous);
        $this->path = $path;
    }

    /**
     * Returns the associated path for the exception.
     *
     * @return string|null
     */
    public function getPath()
    {
        return $this->path;
    }
}
