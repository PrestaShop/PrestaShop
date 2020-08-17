<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Translation\Provider;

use PrestaShopBundle\Translation\Loader\DatabaseTranslationReader;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Mock database loader that will fetch from the provided structure instead of the real database
 */
class MockDatabaseTranslationReader extends DatabaseTranslationReader
{
    /**
     * @var array
     */
    private $databaseContent;

    /**
     * @param array<array{lang: string, key: string, translation: string, domain: string, theme: ?string}> $databaseContent
     */
    public function __construct(array $databaseContent)
    {
        $this->databaseContent = $databaseContent;
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $locale, string $domainSearch, ?string $theme = null): MessageCatalogue
    {
        $catalogue = new MessageCatalogue($locale);

        foreach ($this->databaseContent as $item) {
            if (
                $item['lang'] === $locale
                && preg_match("/$domainSearch/", $item['domain'])
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
