<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Metadata\Resource\Factory;

use Error;
use ReflectionAttribute;

/**
 * A resource written for a newer core can't be instantiated on this one: PHP fails while evaluating its attributes,
 * on an operation class or a named argument this core doesn't have. Any other error (a missing class of the module, an
 * error raised by a constructor...) is a real one, and is not considered here.
 */
trait UnsupportedApiResourceTrait
{
    protected function isUnsupportedApiResource(Error $error): bool
    {
        // Raised by the declaration of the attributes itself, not by the code they run
        $origin = $error->getTrace()[0] ?? [];
        if (ReflectionAttribute::class !== ($origin['class'] ?? null) || 'newInstance' !== ($origin['function'] ?? null)) {
            return false;
        }

        // The class the argument was meant for is unknown, an operation of the core is the only one a resource instantiates
        if (str_starts_with($error->getMessage(), 'Unknown named parameter ')) {
            return true;
        }

        // Only the core evolves with its version: operations of PrestaShopBundle, value objects of the domain
        if (preg_match('/^Class "(.+)" not found$/', $error->getMessage(), $matches)) {
            foreach (['PrestaShopBundle\\', 'PrestaShop\\PrestaShop\\'] as $coreNamespace) {
                if (str_starts_with($matches[1], $coreNamespace)) {
                    return true;
                }
            }
        }

        return false;
    }
}
