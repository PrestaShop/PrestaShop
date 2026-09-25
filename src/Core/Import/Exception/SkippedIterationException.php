<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Exception;

/**
 * Class SkippedIterationException thrown when an import iteration is skipped.
 *
 * @deprecated since 9.3, will be removed in the next major version - replaced by the import engine, which skips rows through PhaseBatchResult::$newlySkippedRows
 */
class SkippedIterationException extends ImportException
{
}
