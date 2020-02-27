<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
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
    public function __construct($message = '', $code = 0, Throwable $previous = null, $path = null)
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
