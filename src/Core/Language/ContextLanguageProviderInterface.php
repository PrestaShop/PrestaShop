<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Language;

/**
 * @experimental This will be refactored once the Context replacement architecture has been decided
 */
interface ContextLanguageProviderInterface
{
    public function getLanguageId(): int;
}
