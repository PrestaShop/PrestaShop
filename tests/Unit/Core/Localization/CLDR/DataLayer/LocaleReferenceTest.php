<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization\CLDR\DataLayer;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\DataLayer\LocaleReference as CldrLocaleReferenceDataLayer;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData as CldrLocaleData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ReaderInterface;

class LocaleReferenceTest extends TestCase
{
    /**
     * The tested data layer
     *
     * @var CldrLocaleReferenceDataLayer
     */
    protected $layer;

    protected $stubLocaleData;

    protected function setUp(): void
    {
        $this->stubLocaleData = new CldrLocaleData();
        /* @phpstan-ignore-next-line */
        $this->stubLocaleData->foo = ['bar', 'baz'];

        $fakeReader = $this->getMockBuilder(ReaderInterface::class)
            ->onlyMethods(['readLocaleData'])
            ->getMock();
        $fakeReader->method('readLocaleData')
            ->willReturnMap([
                ['fr-FR', $this->stubLocaleData],
                ['un-KNOWN', null], // Simulates an unknown locale
            ]);

        /* @var ReaderInterface $fakeReader */
        $this->layer = new CldrLocaleReferenceDataLayer($fakeReader);
    }

    /**
     * Given a valid CldrLocaleReferenceDataLayer object
     * When asking it for a given locale's data
     * Then the expected CLDR LocaleData object should be retrieved, of null if locale code is unknown.
     */
    public function testRead()
    {
        $cldrLocaleData = $this->layer->read('fr-FR');

        $this->assertInstanceOf(
            CldrLocaleData::class,
            $cldrLocaleData
        );

        $cldrLocaleData = $this->layer->read('un-KNOWN');

        $this->assertNull($cldrLocaleData);
    }
}
