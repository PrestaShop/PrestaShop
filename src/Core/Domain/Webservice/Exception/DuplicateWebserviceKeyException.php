<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\Exception;

/**
 * Is thrown when duplicate service key is encountered (e.g. when creating new webservice key which already exists)
 */
class DuplicateWebserviceKeyException extends WebserviceException
{
}
