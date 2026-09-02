<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\QuickAccess;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * Define the contract to access Quick Accesses.
 */
interface QuickAccessRepositoryInterface
{
    /**
     * Returns the complete list of quick accesses.
     *
     * @param LanguageId $languageId
     *
     * @return array
     */
    public function fetchAll(LanguageId $languageId): array;
}
