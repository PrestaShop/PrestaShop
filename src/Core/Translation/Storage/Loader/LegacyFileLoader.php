<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Loader;

use PrestaShop\PrestaShop\Core\Translation\Exception\InvalidLegacyTranslationKeyException;
use PrestaShop\PrestaShop\Core\Translation\Storage\Normalizer\DomainNormalizer;
use PrestaShop\TranslationToolsBundle\Translation\Helper\DomainHelper;
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
     * @throws InvalidLegacyTranslationKeyException
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
    private function buildDomain(LegacyTranslationKey $translationKey): string
    {
        $newDomain = DomainHelper::buildModuleDomainFromLegacySource(
            $translationKey->getModule(),
            $translationKey->getSource()
        );

        return $this->domainNormalizer->normalize($newDomain);
    }
}
