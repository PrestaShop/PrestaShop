<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * PrestaShop forms needs custom domain name for field constraints
 * This feature is not available in Symfony so we need to inject the translator
 * for constraints messages only.
 */
abstract class TranslatorAwareType extends CommonAbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * All languages available on shop. Used for translations.
     *
     * @param array<int, array<string, mixed>> $locales
     */
    protected $locales;

    public function __construct(TranslatorInterface $translator, array $locales)
    {
        $this->translator = $translator;
        $this->locales = $locales;
    }

    /**
     * Get the translated chain from key.
     *
     * @param string $key the key to be translated
     * @param string $domain the domain to be selected
     * @param array $parameters Optional, pass parameters if needed (uncommon)
     *
     * @returns string
     */
    protected function trans($key, $domain, $parameters = [])
    {
        return $this->translator->trans($key, $parameters, $domain);
    }

    /**
     * @return TranslatorInterface
     */
    protected function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    /**
     * Get locales to be used in form type.
     *
     * @return array
     */
    protected function getLocaleChoices()
    {
        $locales = [];

        foreach ($this->locales as $locale) {
            $locales[$locale['name']] = $locale['iso_code'];
        }

        return $locales;
    }
}
