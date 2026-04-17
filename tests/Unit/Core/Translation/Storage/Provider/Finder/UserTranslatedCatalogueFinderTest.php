<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Translation\Storage\Provider\Finder;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Translation\Storage\Loader\DatabaseTranslationLoader;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CatalogueLayersProviderInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder\UserTranslatedCatalogueFinder;
use Symfony\Component\Translation\MessageCatalogue;

class UserTranslatedCatalogueFinderTest extends TestCase
{
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

    /**
     * @var MockObject|DatabaseTranslationLoader
     */
    private $databaseTranslationLoader;

    protected function setUp(): void
    {
        $catalogue = new MessageCatalogue(CatalogueLayersProviderInterface::DEFAULT_LOCALE);
        foreach (self::$wordings as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }

        $this->databaseTranslationLoader = $this->createMock(DatabaseTranslationLoader::class);
        $this->databaseTranslationLoader
            ->method('load')
            ->willReturn($catalogue);
    }

    public function testItFailsWhenTranslationDomainsAreNotStrings()
    {
        $this->expectException(InvalidArgumentException::class);
        /* @phpstan-ignore-next-line */
        new UserTranslatedCatalogueFinder($this->databaseTranslationLoader, ['domain', 1]);
    }

    public function testGetCatalogueFilters()
    {
        $catalogue = (new UserTranslatedCatalogueFinder(
            $this->databaseTranslationLoader,
            ['#^Shop([A-Z]|\.|$)#']
        ))
            ->getCatalogue(CatalogueLayersProviderInterface::DEFAULT_LOCALE);

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'ShopSomeDomain',
            'ShopSomethingElse',
        ], $domains);
    }

    public function testGetCatalogueMessages()
    {
        $provider = new UserTranslatedCatalogueFinder(
            $this->databaseTranslationLoader,
            ['#^Shop([A-Z]|\.|$)#']
        );

        $catalogue = $provider->getCatalogue(CatalogueLayersProviderInterface::DEFAULT_LOCALE);

        $messages = $catalogue->all();
        foreach (self::$wordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }
}
