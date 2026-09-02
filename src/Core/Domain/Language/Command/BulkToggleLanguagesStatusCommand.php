<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\Command;

use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Enables/disables languages status
 */
class BulkToggleLanguagesStatusCommand implements ToggleLanguageStatusCommandInterface
{
    /**
     * @var LanguageId[]
     */
    private $languageIds = [];

    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @param int[] $languageIds
     * @param bool $expectedStatus
     */
    public function __construct(array $languageIds, $expectedStatus)
    {
        $this->assertStatusIsBool($expectedStatus);

        $this->setLanguages($languageIds);
        $this->expectedStatus = $expectedStatus;
    }

    /**
     * @return LanguageId[]
     */
    public function getLanguageIds()
    {
        return $this->languageIds;
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->expectedStatus;
    }

    /**
     * @param int[] $languageIds
     */
    private function setLanguages(array $languageIds)
    {
        if (empty($languageIds)) {
            throw new LanguageConstraintException('Languages must be provided in order to toggle their status');
        }

        foreach ($languageIds as $languageId) {
            $this->languageIds[] = new LanguageId($languageId);
        }
    }

    /**
     * @param bool $status
     *
     * @throws LanguageConstraintException
     */
    private function assertStatusIsBool($status)
    {
        if (!is_bool($status)) {
            throw new LanguageConstraintException('Invalid status provided, language status must be type of "bool"');
        }
    }
}
