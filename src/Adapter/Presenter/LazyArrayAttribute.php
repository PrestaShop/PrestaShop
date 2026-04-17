<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter;

use Attribute;

#[Attribute]
class LazyArrayAttribute
{
    public function __construct(
        public bool $arrayAccess = false,
        public ?string $indexName = null,
        public ?bool $isRewritable = null
    ) {
    }
}
