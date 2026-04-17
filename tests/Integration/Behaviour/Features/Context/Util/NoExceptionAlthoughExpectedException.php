<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Util;

use Exception;

/**
 * NoExceptionAlthoughExpectedException is thrown when code was expecting an Exception
 * to be thrown but did not catch one
 */
class NoExceptionAlthoughExpectedException extends Exception
{
}
