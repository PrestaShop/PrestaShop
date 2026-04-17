<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation;

use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;

/**
 * Replacement for the original Symfony FrameworkBundle translator
 */
class Translator extends BaseTranslator implements TranslatorInterface
{
    use PrestaShopTranslatorTrait;
    use TranslatorLanguageTrait;

    /**
     * {@inheritdoc}
     */
    public function addResource($format, $resource, $locale, $domain = null): void
    {
        parent::addResource($format, $resource, $locale, $domain);
        parent::addResource('db', $domain . '.' . $locale . '.db', $locale, $domain);
    }
}
