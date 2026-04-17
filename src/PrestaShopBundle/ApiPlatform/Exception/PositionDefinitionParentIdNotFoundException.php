<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Exception;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Is thrown when the parentId field property is not in data.
 */
class PositionDefinitionParentIdNotFoundException extends InvalidArgumentException
{
}
