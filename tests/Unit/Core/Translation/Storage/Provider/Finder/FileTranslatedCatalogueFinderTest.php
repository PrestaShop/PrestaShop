<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Translation\Storage\Provider\Finder;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Translation\Exception\TranslationFilesNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CatalogueLayersProviderInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder\FileTranslatedCatalogueFinder;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\MessageCatalogue;

class FileTranslatedCatalogueFinderTest extends TestCase
{
    /**
     * @var string
     */
    private static $tempDir;

    public static function setUpBeforeClass(): void
    {
        self::$tempDir = implode(
            DIRECTORY_SEPARATOR,
            [sys_get_temp_dir(), 'FileTranslatedCatalogueFinderTest']
        );
        $wordings = [
            'ShopSomeDomain' => [
                'Some wording' => 'Some wording',
                'Some other wording' => 'Some other wording',
            ],
            'ShopSomethingElse' => [
                'Foo' => 'Foo',
                'Bar' => 'Bar',
            ],
        ];
        $catalogue = new MessageCatalogue(CatalogueLayersProviderInterface::DEFAULT_LOCALE);
        foreach ($wordings as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        (new XliffFileDumper())->dump($catalogue, [
            'path' => self::$tempDir . DIRECTORY_SEPARATOR . CatalogueLayersProviderInterface::DEFAULT_LOCALE,
            'split_files' => false,
        ]);
    }

    public function testItFailsWhenDirectoryNotExists()
    {
        $this->expectException(TranslationFilesNotFoundException::class);
        new FileTranslatedCatalogueFinder('someFakeDirectory', ['filter']);
    }

    public function testItFailsWhenFiltersAreNotStrings()
    {
        $this->expectException(InvalidArgumentException::class);
        new FileTranslatedCatalogueFinder('/tmp', ['filter', 1]);
    }

    public function testGetCatalogueFilters()
    {
        $catalogue = (new FileTranslatedCatalogueFinder(
            self::$tempDir,
            ['#^Shop([A-Z]|\.|$)#']
        ))
            ->getCatalogue(CatalogueLayersProviderInterface::DEFAULT_LOCALE);

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'ShopSomeDomain',
            'ShopSomethingElse',
        ], $domains);

        $provider = new FileTranslatedCatalogueFinder(
            self::$tempDir,
            ['#^ShopSomething([A-Z]|\.|$)#']
        );
        $catalogue = $provider->getCatalogue(CatalogueLayersProviderInterface::DEFAULT_LOCALE);

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'ShopSomethingElse',
        ], $domains);
    }

    public function testGetCatalogueMessages()
    {
        $expectedWordings = [
            'ShopSomeDomain' => [
                'Some wording' => 'Some wording',
                'Some other wording' => 'Some other wording',
            ],
            'ShopSomethingElse' => [
                'Foo' => 'Foo',
                'Bar' => 'Bar',
            ],
        ];

        $provider = new FileTranslatedCatalogueFinder(
            self::$tempDir,
            ['#^Shop([A-Z]|\.|$)#']
        );

        $catalogue = $provider->getCatalogue(CatalogueLayersProviderInterface::DEFAULT_LOCALE);

        $messages = $catalogue->all();
        ksort($messages);

        $this->assertSame($expectedWordings, $messages);
    }
}
