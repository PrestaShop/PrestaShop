<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Factory;

use Exception;

/**
 * Thrown if no provider is found for the selected identifier.
 */
class ProviderNotFoundException extends Exception
{
    public function __construct($identifier)
    {
        parent::__construct(sprintf('The provider with the identifier %s is not found.', $identifier), 0, null);
    }
}
