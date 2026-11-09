<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Meta;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Meta\MetaDataProvider;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\DefaultMetaPageNameChoiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\Resources\DatabaseDump;

final class MetaTest extends TestCase
{
    protected function setUp(): void
    {
        DatabaseDump::restoreTables(['meta', 'meta_lang']);
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['meta', 'meta_lang']);
    }

    /**
     * @dataProvider listingPageProvider
     */
    public function testConfiguredPageIsExcludedUntilItsRecordIsDeleted(string $page, string $legacyPage): void
    {
        $provider = new MetaDataProvider();
        $id = (int) $provider->getIdByPage($page);
        self::assertGreaterThan(0, $id);
        self::assertContains($page, Meta::getPages(false));
        self::assertNotContains($page, $provider->getAvailablePages());
        self::assertSame($page, $this->getEditChoices($id)[$page]);

        self::assertTrue((new Meta($id))->delete());
        self::assertContains($page, $provider->getAvailablePages());
        self::assertNotContains($legacyPage, $provider->getAvailablePages());
    }

    /**
     * @dataProvider listingPageProvider
     */
    public function testLegacyPageNameRemainsEditable(string $page, string $legacyPage): void
    {
        $provider = new MetaDataProvider();
        $id = (int) $provider->getIdByPage($page);
        self::assertGreaterThan(0, $id);
        $meta = new Meta($id);
        $meta->page = $legacyPage;
        self::assertTrue($meta->update());

        self::assertSame($legacyPage, $this->getEditChoices($id)[$legacyPage]);
        self::assertNotContains($legacyPage, $provider->getAvailablePages());
        self::assertContains($page, $provider->getAvailablePages());
    }

    public static function listingPageProvider(): iterable
    {
        yield 'promotions' => ['prices-drop', 'pricesdrop'];
        yield 'new products' => ['new-products', 'newproducts'];
        yield 'best sellers' => ['best-sales', 'bestsales'];
    }

    private function getEditChoices(int $id): array
    {
        $request = new Request();
        $request->attributes->set('metaId', $id);
        $stack = new RequestStack();
        $stack->push($request);

        return (new DefaultMetaPageNameChoiceProvider($stack, new MetaDataProvider()))->getChoices();
    }
}
