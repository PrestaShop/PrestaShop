<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Factory;

use PrestaShopBundle\Translation\Provider\AbstractProvider;
use PrestaShopBundle\Translation\View\TreeBuilder;

/**
 * This class returns a collection of translations, using a locale and an identifier.
 *
 * Returns MessageCatalogue object or Translation tree array.
 */
class TranslationsFactory implements TranslationsFactoryInterface
{
    /**
     * @var array the list of translation providers
     */
    private $providers = [];

    /**
     * {@inheritdoc}
     */
    public function createCatalogue($domainIdentifier, $locale = 'en_US')
    {
        foreach ($this->providers as $provider) {
            if ($domainIdentifier === $provider->getIdentifier()) {
                return $provider->setLocale($locale)->getMessageCatalogue();
            }
        }

        throw new ProviderNotFoundException($domainIdentifier);
    }

    /**
     * @param string $domainIdentifier
     * @param string $locale
     * @param string|null $theme
     * @param string|null $search
     *
     * @return array|mixed
     *
     * @throws ProviderNotFoundException
     */
    public function createTranslationsArray(
        $domainIdentifier,
        $locale = self::DEFAULT_LOCALE,
        $theme = null,
        $search = null
    ) {
        foreach ($this->providers as $provider) {
            if ($domainIdentifier === $provider->getIdentifier()) {
                $treeBuilder = new TreeBuilder($locale, $theme);

                return $treeBuilder->makeTranslationArray($provider, $search);
            }
        }

        throw new ProviderNotFoundException($domainIdentifier);
    }

    /**
     * @param AbstractProvider $provider
     */
    public function addProvider(AbstractProvider $provider)
    {
        $this->providers[] = $provider;
    }
}
