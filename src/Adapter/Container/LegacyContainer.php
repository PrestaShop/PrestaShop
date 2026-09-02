<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Container;

use Symfony\Component\DependencyInjection\Container;

/**
 * This class is only used when we build containers for legacy environment. It
 * is used as a base class when dumping the container in our ContainerBuilder.
 *
 * It implements LegacyContainerInterface which allows to detect if the container
 * was built by Symfony or by PrestaShop.
 */
class LegacyContainer extends Container implements LegacyContainerInterface
{
}
