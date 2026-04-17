<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\Command;

use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Enables or disables language based in given status
 */
class ToggleLanguageStatusCommand implements ToggleLanguageStatusCommandInterface
{
    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @param int $languageId
     * @param bool $expectedStatus
     *
     * @throws LanguageConstraintException Is thrown when invalid data is provided
     */
    public function __construct($languageId, $expectedStatus)
    {
        $this->assertStatusIsBool($expectedStatus);

        $this->expectedStatus = $expectedStatus;
        $this->languageId = new LanguageId($languageId);
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->expectedStatus;
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
