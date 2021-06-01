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

namespace Tests\Integration\Core\Translation\Storage\Provider;

use PrestaShop\PrestaShop\Core\Translation\Storage\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Mock database loader that will fetch from the provided structure instead of the real database
 */
class MockDatabaseTranslationLoader extends DatabaseTranslationLoader
{
    /**
     * @var array
     */
    private $databaseContent;

    /**
     * @param array<array{lang: string, key: string, translation: string, domain: string, theme: ?string}> $databaseContent
     */
    public function __construct(array $databaseContent, $languageRepository, $translationRepository)
    {
        $this->databaseContent = $databaseContent;

        parent::__construct($languageRepository, $translationRepository);
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $locale, string $domainSearch = 'messages', ?string $theme = null): MessageCatalogue
    {
        $catalogue = new MessageCatalogue($locale);

        foreach ($this->databaseContent as $item) {
            $domainMatches = ('*' === $domainSearch) ?: (bool) preg_match("/$domainSearch/", $item['domain']);

            if (
                $item['lang'] === $locale
                && $domainMatches
                && $item['theme'] === $theme
            ) {
                $catalogue->add(
                    [$item['key'] => $item['translation']],
                    $item['domain']
                );
            }
        }

        return $catalogue;
    }
}
