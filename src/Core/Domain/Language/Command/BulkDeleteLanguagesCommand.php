<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\Command;

use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Deletes given languages
 */
class BulkDeleteLanguagesCommand
{
    /**
     * @var LanguageId[]
     */
    private $languageIds = [];

    /**
     * @param int[] $languageIds
     */
    public function __construct(array $languageIds)
    {
        $this->setLanguageIds($languageIds);
    }

    /**
     * @return LanguageId[]
     */
    public function getLanguageIds()
    {
        return $this->languageIds;
    }

    /**
     * @param int[] $languageIds
     */
    private function setLanguageIds(array $languageIds)
    {
        if (empty($languageIds)) {
            throw new LanguageConstraintException('At least one language must be provided for deleting', LanguageConstraintException::EMPTY_BULK_DELETE);
        }

        foreach ($languageIds as $languageId) {
            $this->languageIds[] = new LanguageId($languageId);
        }
    }
}
