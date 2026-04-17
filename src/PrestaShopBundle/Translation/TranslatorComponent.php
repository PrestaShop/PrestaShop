<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation;

use Symfony\Component\Translation\Translator as BaseTranslatorComponent;

/**
 * Translator used by Context
 */
class TranslatorComponent extends BaseTranslatorComponent implements TranslatorInterface
{
    use PrestaShopTranslatorTrait;
    use TranslatorLanguageTrait;
}
