<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Language;

use Language;
use PrestaShop\PrestaShop\Core\Language\LanguageValidatorInterface;

/**
 * Class LanguageValidator is responsible for supporting validations from legacy Language class part.
 */
final class LanguageValidator implements LanguageValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isInstalledByLocale($locale)
    {
        return Language::isInstalledByLocale($locale);
    }
}
