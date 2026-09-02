<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Container;

use Symfony\Component\DependencyInjection\ContainerBuilder as SfContainerBuilder;

/**
 * This class is only used when we build containers for legacy environment. It
 * is used as a base builder when we build the container in our ContainerBuilder.
 *
 * It is needed during the first generation of the container as it is used as a container
 * during this process, next calls usually use the built container class that extends LegacyContainer.
 *
 * It implements LegacyContainerInterface which allows to detect if the container
 * was built by Symfony or by PrestaShop.
 */
class LegacyContainerBuilder extends SfContainerBuilder implements LegacyContainerInterface
{
}
