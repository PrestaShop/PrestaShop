<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\Command;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Deletes given languages
 */
class DeleteLanguageCommand
{
    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @param int $languageId
     */
    public function __construct($languageId)
    {
        $this->languageId = new LanguageId($languageId);
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }
}
