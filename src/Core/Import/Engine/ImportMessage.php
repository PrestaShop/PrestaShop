<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine;

/**
 * One structured message produced while processing an import run.
 *
 * Row indexes are 0-based physical record indexes in the working file (the
 * header line is row 0 when one row is skipped); null means a file-level
 * message. The message text is already translated by the importer.
 */
final class ImportMessage
{
    public const SEVERITY_NOTICE = 'notice';
    public const SEVERITY_WARNING = 'warning';
    public const SEVERITY_ERROR = 'error';

    public function __construct(
        public readonly string $severity,
        public readonly string $phase,
        public readonly ?int $row,
        public readonly ?string $field,
        public readonly string $message,
    ) {
    }
}
