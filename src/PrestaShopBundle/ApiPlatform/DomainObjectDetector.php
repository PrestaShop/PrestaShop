<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
