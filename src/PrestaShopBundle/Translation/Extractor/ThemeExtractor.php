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

namespace PrestaShopBundle\Translation\Extractor;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\SmartyExtractor;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Extracts wordings from theme files
 */
class ThemeExtractor implements ThemeExtractorInterface
{
    /**
     * @var SmartyExtractor the Smarty Extractor
     */
    private $smartyExtractor;

    public function __construct(SmartyExtractor $smartyExtractor)
    {
        $this->smartyExtractor = $smartyExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function extract(Theme $theme, $locale = self::DEFAULT_LOCALE, $forceRefresh = false)
    {
        $catalogue = new MessageCatalogue($locale);

        $this->smartyExtractor->extract(
            rtrim($theme->getDirectory(), '/'),
            $catalogue
        );

        $catalogue = $this->normalize($catalogue);

        return $catalogue;
    }

    /**
     * Normalizes domains in a catalogue by removing dots
     *
     * @param MessageCatalogue $catalogue
     *
     * @return MessageCatalogue
     */
    private function normalize(MessageCatalogue $catalogue)
    {
        $newCatalogue = new MessageCatalogue($catalogue->getLocale());

        foreach ($catalogue->all() as $domain => $messages) {
            $newDomain = $this->normalizeDomain($domain);
            $newCatalogue->add($messages, $newDomain);
        }

        foreach ($catalogue->getMetadata('', '') as $domain => $metadata) {
            $newDomain = $this->normalizeDomain($domain);
            foreach ($metadata as $key => $value) {
                $newCatalogue->setMetadata($key, $value, $newDomain);
            }
        }

        return $newCatalogue;
    }

    /**
     * @param string $domain
     *
     * @return string
     */
    private function normalizeDomain($domain)
    {
        return strtr($domain, ['.' => '']);
    }
}
