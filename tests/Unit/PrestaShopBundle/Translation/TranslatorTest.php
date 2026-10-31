<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Translation;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Loader\ExtraPropertyTranslationLoader;
use PrestaShopBundle\Translation\Translator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

class TranslatorTest extends TestCase
{
    private const LOCALE = 'en-US';

    public function testDatabaseAndRegistryWordingsAreMergedInOrder(): void
    {
        $databaseLoader = $this->createDatabaseLoader([
            'Domain1' => ['Translated wording' => 'db translated'],
        ]);
        $extraPropertyLoader = $this->createExtraPropertyLoader([
            'Domain1' => ['key1' => 'key1', 'Translated wording' => 'Translated wording', 'New wording' => 'New wording'],
        ]);
        $translator = $this->createTranslator($databaseLoader, $extraPropertyLoader);

        $catalogue = $translator->getCatalogue(self::LOCALE);

        $this->assertSame([self::LOCALE], $databaseLoader->loadedLocales, 'The database is queried once for the whole locale');
        $this->assertSame('db translated', $catalogue->get('Translated wording', 'Domain1'), 'An admin translation beats a registry wording');
        $this->assertSame('file 1', $catalogue->get('key1', 'Domain1'), 'A registry wording never overrides an existing key');
        $this->assertSame('New wording', $catalogue->get('New wording', 'Domain1'), 'A registry wording missing from the catalogue is added');
    }

    public function testFallbackLocaleIsNotCompleted(): void
    {
        $databaseLoader = $this->createDatabaseLoader(['Domain1' => ['key1' => 'db 1']]);
        $extraPropertyLoader = $this->createExtraPropertyLoader(['Domain1' => ['New wording' => 'New wording']]);
        $translator = $this->createTranslator($databaseLoader, $extraPropertyLoader);
        $translator->setFallbackLocales(['default']);
        $translator->addResource('file', ['key1' => 'default 1'], 'default', 'Domain1');

        $translator->getCatalogue(self::LOCALE);

        $this->assertContains(self::LOCALE, $databaseLoader->loadedLocales);
        $this->assertNotContains('default', $databaseLoader->loadedLocales, 'The "default" fallback is not queried in database');
        $this->assertNotContains('default', $extraPropertyLoader->requestedLocales, 'The "default" fallback gets no registry wordings');
        $this->assertSame('default 1', $translator->getCatalogue('default')->get('key1', 'Domain1'));
    }

    private function createTranslator(LoaderInterface $databaseLoader, ExtraPropertyTranslationLoader $extraPropertyLoader): Translator
    {
        $translator = new Translator(new Container(), new MessageFormatter(), self::LOCALE);
        $translator->addLoader('file', new class() implements LoaderInterface {
            public function load(mixed $resource, string $locale, string $domain = 'messages'): MessageCatalogue
            {
                $catalogue = new MessageCatalogue($locale, [$domain => $resource]);
                $catalogue->addResource(new FileResource(__FILE__));

                return $catalogue;
            }
        });
        $translator->addLoader('db', $databaseLoader);
        $translator->setExtraPropertyTranslationLoader($extraPropertyLoader);
        $translator->addResource('file', ['key1' => 'file 1', 'Translated wording' => 'Translated wording'], self::LOCALE, 'Domain1');

        return $translator;
    }

    /**
     * @param array<string, array<string, string>> $messagesByDomain
     *
     * @return LoaderInterface&object{loadedLocales: string[]}
     */
    private function createDatabaseLoader(array $messagesByDomain): LoaderInterface
    {
        return new class($messagesByDomain) implements LoaderInterface {
            /** @var string[] */
            public array $loadedLocales = [];

            public function __construct(
                private readonly array $messagesByDomain,
            ) {
            }

            public function load(mixed $resource, string $locale, string $domain = 'messages'): MessageCatalogue
            {
                $this->loadedLocales[] = $locale;

                return new MessageCatalogue($locale, $this->messagesByDomain);
            }
        };
    }

    /**
     * @param array<string, array<string, string>> $wordingsByDomain
     *
     * @return ExtraPropertyTranslationLoader&object{requestedLocales: string[]}
     */
    private function createExtraPropertyLoader(array $wordingsByDomain): ExtraPropertyTranslationLoader
    {
        return new class($wordingsByDomain) extends ExtraPropertyTranslationLoader {
            /** @var string[] */
            public array $requestedLocales = [];

            public function __construct(
                private readonly array $wordingsByDomain,
            ) {
            }

            public function getNormalizedDomains(string $locale): array
            {
                $this->requestedLocales[] = $locale;

                return array_keys($this->wordingsByDomain);
            }

            public function load($resource, $locale, $domain = 'messages'): MessageCatalogue
            {
                return new MessageCatalogue($locale, [$domain => $this->wordingsByDomain[$domain] ?? []]);
            }
        };
    }
}
