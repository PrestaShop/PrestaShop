<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Language;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Language\LanguageDefaultFontsCatalog;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;

class LanguageDefaultFontsCatalogTest extends TestCase
{
    public function testConstructor()
    {
        $fontCatalog = new LanguageDefaultFontsCatalog();
        $this->assertNotNull($fontCatalog);

        $fontCatalog = new LanguageDefaultFontsCatalog([
            'ar' => 'Tahoma',
            'fa' => 'Tahoma',
        ]);
        $this->assertNotNull($fontCatalog);
    }

    public function testGetDefaultFont()
    {
        $fontCatalog = new LanguageDefaultFontsCatalog([
            'ar' => 'Tahoma',
            'fa' => 'Tahoma',
            'fr' => 'Comic Sans MS',
        ]);
        $this->assertNotNull($fontCatalog);

        $font = $fontCatalog->getDefaultFontByLanguage($this->buildLanguageMock('ar'));
        $this->assertEquals('Tahoma', $font);

        $font = $fontCatalog->getDefaultFontByLanguage($this->buildLanguageMock('fr'));
        $this->assertEquals('Comic Sans MS', $font);

        $font = $fontCatalog->getDefaultFontByLanguage($this->buildLanguageMock('en'));
        $this->assertEquals('', $font);
    }

    /**
     * @param string $isoCode
     *
     * @return MockObject|LanguageInterface
     */
    private function buildLanguageMock($isoCode)
    {
        $languageMock = $this->getMockBuilder(LanguageInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $languageMock
            ->expects($this->once())
            ->method('getIsoCode')
            ->willReturn($isoCode)
        ;

        return $languageMock;
    }
}
