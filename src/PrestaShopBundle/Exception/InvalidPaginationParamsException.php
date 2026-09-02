<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Exception;

use Exception;
use OutOfBoundsException;

class InvalidPaginationParamsException extends OutOfBoundsException
{
    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(
        $message = '',
        $code = 0,
        ?Exception $previous = null
    ) {
        if ($message == '') {
            $message = 'A page index should be an integer greater than 1.';
        }

        parent::__construct($message, $code, $previous);
    }
}
