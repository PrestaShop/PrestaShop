<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Gets language for editing in Back Office
 */
class GetLanguageForEditing
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
