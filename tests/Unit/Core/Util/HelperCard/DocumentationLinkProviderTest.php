<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Util\HelperCard;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\HelperCard\DocumentationLinkProvider;
use PrestaShop\PrestaShop\Core\Util\HelperCard\DocumentationLinkProviderInterface;
use PrestaShop\PrestaShop\Core\Util\HelperCard\HelperCardDocumentationDoesNotExistException;

class DocumentationLinkProviderTest extends TestCase
{
    public function testIsValidImplementation()
    {
        $provider = new DocumentationLinkProvider(
            'FR',
            ['seo_card' => [
                'FR' => 'https://doc.presta.com/fr/seo',
                'EN' => 'https://doc.presta.com/en/seo',
                '_fallback' => 'https://doc.presta.com/seo',
            ]]
        );

        $this->assertInstanceOf(DocumentationLinkProviderInterface::class, $provider);
    }

    public function testGetFRValidLink()
    {
        $provider = new DocumentationLinkProvider(
            'FR',
            ['seo_card' => [
                'FR' => 'https://doc.presta.com/fr/seo',
                'EN' => 'https://doc.presta.com/en/seo',
                '_fallback' => 'https://doc.presta.com/seo',
            ]]
        );

        $link = $provider->getLink('seo_card');

        $this->assertEquals('https://doc.presta.com/fr/seo', $link);
    }

    public function testGetBadCardLink()
    {
        $provider = new DocumentationLinkProvider(
            'FR',
            ['seo_card' => [
                'FR' => 'https://doc.presta.com/fr/seo',
                'EN' => 'https://doc.presta.com/en/seo',
                '_fallback' => 'https://doc.presta.com/seo',
            ]]
        );

        $this->expectException(HelperCardDocumentationDoesNotExistException::class);

        $link = $provider->getLink('aaaa');
    }

    public function testGetITInvalidLinkSoExpectsFallback()
    {
        $provider = new DocumentationLinkProvider(
            'IT',
            ['seo_card' => [
                'FR' => 'https://doc.presta.com/fr/seo',
                'EN' => 'https://doc.presta.com/en/seo',
                '_fallback' => 'https://doc.presta.com/seo',
            ]]
        );

        $link = $provider->getLink('seo_card');

        $this->assertEquals('https://doc.presta.com/seo', $link);
    }

    public function testGetEZInvalidLinkWithoutFallback()
    {
        $provider = new DocumentationLinkProvider(
            'EZ',
            ['seo_card' => [
                'FR' => 'https://doc.presta.com/fr/seo',
                'EN' => 'https://doc.presta.com/en/seo',
            ]]
        );

        $this->expectException(HelperCardDocumentationDoesNotExistException::class);

        $link = $provider->getLink('seo_card');
    }
}
