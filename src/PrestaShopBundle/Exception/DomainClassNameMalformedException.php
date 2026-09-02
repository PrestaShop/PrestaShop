<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

/**
 * Is thrown if the class name of the CQRS list is not a correct class name
 */
class DomainClassNameMalformedException extends DomainException
{
}
