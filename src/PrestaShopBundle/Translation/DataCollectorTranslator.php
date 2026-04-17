<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation;

use Symfony\Component\Translation\DataCollectorTranslator as BaseTranslator;
use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * This is the decorator of the framework DataCollectorTranslator service, it is required mostly
 * for the PrestaShopTranslatorTrait that handles fallback on legacy translation system when useful.
 *
 * We need to explicitly implement some method even if they are just proxies because the TranslatorLanguageLoader
 * checks their presence before calling them.
 */
class DataCollectorTranslator extends BaseTranslator implements TranslatorInterface
{
    use PrestaShopTranslatorTrait;

    public function addLoader(string $format, LoaderInterface $loader)
    {
        return $this->__call('addLoader', [$format, $loader]);
    }

    public function addResource(string $format, mixed $resource, string $locale, ?string $domain = null)
    {
        return $this->__call('addResource', [$format, $resource, $locale, $domain]);
    }

    /**
     * {@inheritdoc}
     */
    public function isLanguageLoaded($locale)
    {
        return $this->__call('isLanguageLoaded', [$locale]);
    }

    /**
     * {@inheritdoc}
     */
    public function clearLanguage($locale)
    {
        return $this->__call('clearLanguage', [$locale]);
    }
}
