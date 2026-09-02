<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Loader;

use PrestaShop\TranslationToolsBundle\Translation\Helper\DomainHelper;
use PrestaShopBundle\Translation\DomainNormalizer;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Able to convert old translation files (in translations/es.php) into
 * Symfony MessageCatalogue objects.
 */
final class LegacyFileLoader implements LoaderInterface
{
    /**
     * @var LegacyFileReader
     */
    private $fileReader;

    /**
     * @var DomainNormalizer
     */
    private $domainNormalizer;

    /**
     * @param LegacyFileReader $fileReader
     */
    public function __construct(LegacyFileReader $fileReader)
    {
        $this->fileReader = $fileReader;
        $this->domainNormalizer = new DomainNormalizer();
    }

    /**
     * {@inheritdoc}
     *
     * Note that parameter "domain" is useless, as domain is inferred from source files
     *
     * @throws \PrestaShopBundle\Translation\Exception\InvalidLegacyTranslationKeyException
     */
    public function load($path, $locale, $domain = 'messages'): MessageCatalogue
    {
        $catalogue = new MessageCatalogue($locale);

        $tokens = $this->fileReader->load($path, $locale);

        foreach ($tokens as $translationKey => $translationValue) {
            $parsed = LegacyTranslationKey::buildFromString($translationKey);
            $id = $parsed->getHash();
            $catalogue->set($id, $translationValue, $this->buildDomain($parsed));
        }

        return $catalogue;
    }

    /**
     * Builds the domain using information in the translation key
     *
     * @param LegacyTranslationKey $translationKey
     *
     * @return string
     */
    private function buildDomain(LegacyTranslationKey $translationKey)
    {
        $newDomain = DomainHelper::buildModuleDomainFromLegacySource(
            $translationKey->getModule(),
            $translationKey->getSource()
        );

        return $this->domainNormalizer->normalize($newDomain);
    }
}
