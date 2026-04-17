<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Contact\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

/**
 * An abstraction for all contact related exceptions. Use this one in catch clause to detect all related exceptions.
 */
class ContactException extends DomainException
{
}
