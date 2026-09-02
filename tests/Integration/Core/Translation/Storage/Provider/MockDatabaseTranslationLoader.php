<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
