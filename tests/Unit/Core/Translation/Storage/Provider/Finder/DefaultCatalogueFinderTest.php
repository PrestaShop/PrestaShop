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
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder\DefaultCatalogueFinder;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\MessageCatalogue;

class DefaultCatalogueFinderTest extends TestCase
{
    /**
     * @var string
     */
    private static $tempDir;

    private static $wordings = [
        'ShopSomeDomain' => [
            'Some wording' => 'Some wording',
            'Some other wording' => 'Some other wording',
        ],
        'ShopSomethingElse' => [
            'Foo' => 'Foo',
            'Bar' => 'Bar',
        ],
    ];

    private static $emptyWordings = [
        'ShopSomeDomain.en-US' => [
            'Some wording' => '',
            'Some other wording' => '',
        ],
        'ShopSomethingElse.en-US' => [
            'Foo' => '',
            'Bar' => '',
        ],
    ];

    public static function setUpBeforeClass(): void
    {
        self::$tempDir = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), 'DefaultCatalogueFinderTest']);

        $catalogue = new MessageCatalogue(CatalogueLayersProviderInterface::DEFAULT_LOCALE);
        foreach (self::$wordings as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        (new XliffFileDumper())->dump($catalogue, [
            'path' => self::$tempDir,
            'split_files' => false,
        ]);
    }

    public function testItFailsWhenDirectoryNotExists()
    {
        $this->expectException(TranslationFilesNotFoundException::class);
        new DefaultCatalogueFinder('someFakeDirectory', ['filter']);
    }

    public function testItFailsWhenFiltersAreNotStrings()
    {
        $this->expectException(InvalidArgumentException::class);
        /* @phpstan-ignore-next-line */
        new DefaultCatalogueFinder('/tmp', ['filter', 1]);
    }

    public function testGetCatalogueFilters()
    {
        $catalogue = (new DefaultCatalogueFinder(
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

        $provider = new DefaultCatalogueFinder(
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
        $provider = new DefaultCatalogueFinder(
            self::$tempDir,
            ['#^Shop([A-Z]|\.|$)#']
        );

        $catalogue = $provider->getCatalogue(CatalogueLayersProviderInterface::DEFAULT_LOCALE);

        $catalogueAsArray = $catalogue->all();
        foreach (self::$wordings as $key => $messages) {
            $this->assertArrayHasKey($key, $catalogueAsArray);
            foreach ($messages as $messageKey => $message) {
                $this->assertArrayHasKey($messageKey, $catalogueAsArray[$key]);
                $this->assertSame('', $catalogueAsArray[$key][$messageKey]);
            }
        }
    }

    public function testGetCatalogue()
    {
        $provider = new DefaultCatalogueFinder(
            self::$tempDir,
            ['#^Shop([A-Z]|\.|$)#']
        );

        $catalogue = $provider->getCatalogue('ab-AB');

        $messages = $catalogue->all();
        ksort($messages);

        $this->assertSame(self::$emptyWordings, $messages);
    }
}
