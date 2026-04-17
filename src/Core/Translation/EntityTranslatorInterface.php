<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation;

/**
 * Translates multi language items in database using DataLang classes
 */
interface EntityTranslatorInterface
{
    /**
     * Executes the translation
     *
     * @param int $languageId
     * @param int $shopId
     */
    public function translate(int $languageId, int $shopId): void;
}
