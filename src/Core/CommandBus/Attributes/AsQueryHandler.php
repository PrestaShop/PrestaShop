<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\CommandBus\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class AsQueryHandler
{
    public function __construct(public $method = 'handle')
    {
    }
}
