<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Command;

use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportMatchConstraintException;

/**
 * Deletes a saved data-matching configuration.
 */
final class DeleteImportMatchCommand
{
    /**
     * @var int
     */
    private $importMatchId;

    /**
     * @throws ImportMatchConstraintException
     */
    public function __construct(int $importMatchId)
    {
        if ($importMatchId <= 0) {
            throw new ImportMatchConstraintException(sprintf('Import match id "%d" is invalid.', $importMatchId), ImportMatchConstraintException::INVALID_ID);
        }

        $this->importMatchId = $importMatchId;
    }

    public function getImportMatchId(): int
    {
        return $this->importMatchId;
    }
}
