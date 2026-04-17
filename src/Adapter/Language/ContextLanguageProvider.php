<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Language;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Language\ContextLanguageProviderInterface;

/**
 * @experimental This will be refactored once the Context replacement architecture is decided
 */
class ContextLanguageProvider implements ContextLanguageProviderInterface
{
    public function __construct(
        protected readonly LegacyContext $context
    ) {
    }

    public function getLanguageId(): int
    {
        $langId = (int) $this->context->getContext()->language->id;

        if (!$langId) {
            throw new CoreException('Context language is missing');
        }

        return $langId;
    }
}
