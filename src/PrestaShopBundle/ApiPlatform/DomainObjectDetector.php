<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\ApiPlatform;

/**
 * This service detects if a class or an object is mart of the Domain namespace, either by checking
 * if it's part of the registered commands and queries. If they are not it checks if they are part
 * of domain namespaces like Value Objects or Query Results.
 */
class DomainObjectDetector
{
    public function __construct(
        protected readonly array $commandsAndQueries,
        protected readonly array $domainNamespaces,
    ) {
    }

    public function isDomainObject(mixed $objectOrType): bool
    {
        // Check the type if a string is provided
        if (is_string($objectOrType) && class_exists($objectOrType)) {
            $objectClass = $objectOrType;
        } elseif (is_object($objectOrType) && class_exists(get_class($objectOrType))) {
            $objectClass = get_class($objectOrType);
        } else {
            return false;
        }

        // CQRS classes are handled by our domain serializer
        if (in_array($objectClass, $this->commandsAndQueries)) {
            return true;
        }

        // Even if the class is not a command itself, but it is part of the domain namespace
        // then it is part of the domain
        foreach ($this->domainNamespaces as $domainNamespace) {
            if (str_starts_with($objectClass, $domainNamespace)) {
                return true;
            }
        }

        return false;
    }
}
