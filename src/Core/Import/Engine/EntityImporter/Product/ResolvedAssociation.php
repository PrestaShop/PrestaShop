<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;

/**
 * Result of resolving one association target (id or null when unresolved,
 * plus any message produced along the way).
 */
final class ResolvedAssociation
{
    /**
     * @param list<ImportMessage> $messages
     */
    public function __construct(
        public readonly ?int $id,
        public readonly array $messages = [],
    ) {
    }
}
