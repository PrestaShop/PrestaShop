<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Translation;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\TranslatorComponent;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

class TranslatorComponentTest extends TestCase
{
    private const LOCALE = 'en-US';

    public function testDatabaseTranslationsAreMergedOnceOverEveryDomain(): void
    {
        $databaseLoader = $this->createDatabaseLoader([
            'Domain1' => ['key1' => 'db 1'],
            'Domain3' => ['key3' => 'db 3'],
        ]);
        $translator = $this->createTranslator($databaseLoader);

        $catalogue = $translator->getCatalogue(self::LOCALE);

        $this->assertSame([self::LOCALE], $databaseLoader->loadedLocales, 'The database is queried once for the whole locale');
        $this->assertSame('db 1', $catalogue->get('key1', 'Domain1'), 'A database translation overrides the file one');
        $this->assertSame('file 2', $catalogue->get('key2', 'Domain1'), 'A file translation without database override is kept');
        $this->assertSame('file 1', $catalogue->get('key1', 'Domain2'), 'A domain without database translations is kept');
        $this->assertSame('db 3', $catalogue->get('key3', 'Domain3'), 'A domain only translated in database is added');
    }

    public function testLocaleWithoutDatabaseLanguageKeepsTheFileTranslations(): void
    {
        $databaseLoader = $this->createDatabaseLoader([], new NotFoundResourceException('Language not found in database: en-US'));
        $translator = $this->createTranslator($databaseLoader);

        $catalogue = $translator->getCatalogue(self::LOCALE);

        $this->assertSame([self::LOCALE], $databaseLoader->loadedLocales);
        $this->assertSame('file 1', $catalogue->get('key1', 'Domain1'));
    }

    public function testLocaleWithoutResourcesIsNotLoadedFromDatabase(): void
    {
        $databaseLoader = $this->createDatabaseLoader(['Domain1' => ['key1' => 'db 1']]);
        $translator = $this->createTranslator($databaseLoader);

        // A module translating its name in every shop language from the loaded translator does this
        $this->assertSame('key1', $translator->trans('key1', [], 'Domain1', 'fr-FR'));

        $this->assertSame([], $databaseLoader->loadedLocales, 'A locale without file resources must stay empty, the translator built for it loads the database itself');
        $this->assertSame('db 1', $translator->trans('key1', [], 'Domain1', self::LOCALE));
    }

    private function createTranslator(LoaderInterface $databaseLoader): TranslatorComponent
    {
        $translator = new TranslatorComponent(self::LOCALE);
        $translator->addLoader('file', new class() implements LoaderInterface {
            public function load(mixed $resource, string $locale, string $domain = 'messages'): MessageCatalogue
            {
                $catalogue = new MessageCatalogue($locale, [$domain => $resource]);
                $catalogue->addResource(new FileResource(__FILE__));

                return $catalogue;
            }
        });
        $translator->addLoader('db', $databaseLoader);
        $translator->addResource('file', ['key1' => 'file 1', 'key2' => 'file 2'], self::LOCALE, 'Domain1');
        $translator->addResource('file', ['key1' => 'file 1'], self::LOCALE, 'Domain2');

        return $translator;
    }

    /**
     * @param array<string, array<string, string>> $messagesByDomain
     *
     * @return LoaderInterface&object{loadedLocales: string[]}
     */
    private function createDatabaseLoader(array $messagesByDomain, ?NotFoundResourceException $exception = null): LoaderInterface
    {
        return new class($messagesByDomain, $exception) implements LoaderInterface {
            /** @var string[] */
            public array $loadedLocales = [];

            public function __construct(
                private readonly array $messagesByDomain,
                private readonly ?NotFoundResourceException $exception,
            ) {
            }

            public function load(mixed $resource, string $locale, string $domain = 'messages'): MessageCatalogue
            {
                $this->loadedLocales[] = $locale;
                if (null !== $this->exception) {
                    throw $this->exception;
                }

                return new MessageCatalogue($locale, $this->messagesByDomain);
            }
        };
    }
}
