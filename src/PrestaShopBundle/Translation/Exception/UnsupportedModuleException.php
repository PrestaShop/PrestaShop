<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Exception;

use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Will be thrown if a module name is required by a provider and not set.
 */
final class UnsupportedModuleException extends NotFoundResourceException
{
    /**
     * @param string $providerIdentifier the provider identifier
     *
     * @return self
     */
    public static function moduleNotProvided($providerIdentifier)
    {
        $exceptionMessage = sprintf(
            'The translation provider with the identifier "%s" require a module to be set.',
            $providerIdentifier
        );

        return new self($exceptionMessage);
    }
}
