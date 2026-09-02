<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing\Converter\Exception;

/**
 * Class AlreadyConvertedException is thrown when trying to converting
 * an already converted url. Thus you can detect no redirection is needed
 * and avoid an infinite loop.
 */
class AlreadyConvertedException extends RoutingException
{
}
