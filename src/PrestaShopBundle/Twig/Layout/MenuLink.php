<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Layout;

class MenuLink
{
    public function __construct(
        public readonly string $name,
        public readonly string $href = '',
        public readonly string $icon = '',
        public readonly array $attributes = [],
    ) {
    }
}
