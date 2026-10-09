<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;
use Validate;

class ValidateCoreTest extends TestCase
{
    /**
     * @dataProvider isIp2LongDataProvider
     */
    public function testIsIp2Long($expected, $input)
    {
        $this->assertEquals($expected, Validate::isIp2Long($input));
    }

    /**
     * @dataProvider isEmailDataProvider
     */
    public function testIsEmail($expected, $input)
    {
        $this->assertSame($expected, Validate::isEmail($input));
    }

    /**
     * @dataProvider isBirthDateProvider
     */
    public function testIsBirthDate($expected, $input)
    {
        // data from isBirthDateProvider provider are in UTC
        $defaultTz = date_default_timezone_get();
        date_default_timezone_set('UTC');
        try {
            $this->assertSame($expected, Validate::isBirthDate($input));
        } finally {
            date_default_timezone_set($defaultTz);
        }
    }

    /**
     * @dataProvider isDateOrNullProvider
     */
    public function testIsDateOrNull($expected, $input)
    {
        $this->assertSame($expected, Validate::isDateOrNull($input));
    }

    /**
     * @dataProvider isMd5DataProvider
     */
    public function testIsMd5($expected, $input)
    {
        $this->assertSame($expected, Validate::isMd5($input));
    }

    /**
     * @dataProvider isSha1DataProvider
     */
    public function testIsSha1($expected, $input)
    {
        $this->assertSame($expected, Validate::isSha1($input));
    }

    /**
     * @dataProvider isNameDataProvider
     */
    public function testIsName($expected, $input)
    {
        $this->assertSame($expected, Validate::isName($input));
    }

    /**
     * @dataProvider isCustomerNameDataProvider
     */
    public function testIsCustomerName($expected, $input)
    {
        $this->assertSame($expected, Validate::isCustomerName($input));
    }

    /**
     * @dataProvider isFloatDataProvider
     */
    public function testIsFloat($expected, $input)
    {
        $this->assertSame($expected, Validate::isFloat($input));
    }

    /**
     * @dataProvider isUnsignedFloatDataProvider
     */
    public function testIsUnsignedFloat($expected, $input)
    {
        $this->assertSame($expected, Validate::isUnsignedFloat($input));
    }

    /**
     * @depends testIsFloat
     *
     * @dataProvider isOptFloatDataProvider
     */
    public function testIsOptFloat($expected, $input)
    {
        $this->assertSame($expected, Validate::isOptFloat($input));
    }

    /**
     * @dataProvider isArrayWithIdsDataProvider
     *
     * @param bool $expected
     * @param string|int|array<string|int|bool|array> $input
     */
    public function testIsArrayWithIds(bool $expected, $input)
    {
        $this->assertSame($expected, Validate::isArrayWithIds($input));
    }

    /**
     * @dataProvider isUrlDataProvider
     *
     * @param bool $expected
     * @param string $url
     */
    public function testIsUrl(bool $expected, string $url): void
    {
        $this->assertEquals($expected, Validate::isUrl($url));
    }

    public function isUrlDataProvider(): iterable
    {
        yield 'test quick access link' => [
            true,
            'index.php?controller=AdminCartRules&addcart_rule',
        ];
    }

    public function isIp2LongDataProvider()
    {
        return [
            [false, 'toto'],
            [true, '123'],
        ];
    }

    public function isMd5DataProvider()
    {
        return [
            [1, md5('SomeRandomString')],
            [0, ''],
            [0, sha1('AnotherRandomString')],
            [0, substr(md5('AnotherRandomString'), 0, 31)],
            [0, 123],
            [0, false],
        ];
    }

    public function isSha1DataProvider()
    {
        return [
            [1, sha1('SomeRandomString')],
            [0, ''],
            [0, md5('AnotherRandomString')],
            [0, substr(sha1('AnotherRandomString'), 0, 39)],
            [0, 123],
            [0, false],
        ];
    }

    public function isNameDataProvider()
    {
        return [
            [1, 'Mathieu'],
            [1, 'Dupont'],
            [1, 'Jaçinthé'],
            [1, 'Jaçinthø'],
            [1, 'John D.'],
            [1, 'John D.John'],
            [1, 'John D. John'],
            [1, 'John D. John D.'],
            [1, 'Mario Bros.'],
            [1, 'ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â'],
            [0, 'https://www.website.com'],
            [1, 'www.website.com'],
            [1, 'www\.website\.com'],
            [1, 'www\\.website\\.com'],
            [1, 'www.website.com.'],
            [1, 'website。com'],
            [1, 'John D. www.some.site'],
            [1, 'www.website.com is cool'],
            [1, 'website。com。'],
            [1, 'website。com'],
            [0, 'website%2Ecom'],
            [1, 'website/./com'],
            [1, '.rn'],
            [1, 'websitecom/a'],
            [0, 'websitecom%20a'],
            [1, '`hello'],
            [1, 'hello[my friend]'],
        ];
    }

    public function isCustomerNameDataProvider()
    {
        return [
            [true, 'Mathieu'],
            [true, 'Dupont'],
            [true, 'Jaçinthé'],
            [true, 'Jaçinthø'],
            [true, 'John D.'],
            [true, 'John D. John'],
            [true, 'John D. John D.'],
            [true, 'Mario Bros.'],
            [true, 'ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â'],
            [false, 'https://www.website.com'],
            [false, 'www.website.com'],
            [false, 'www\.website\.com'],
            [false, 'www\\.website\\.com'],
            [false, 'www.website.com.'],
            [false, 'website。com'],
            [false, 'John D.John'],
            [false, 'John D. www.some.site'],
            [false, 'www.website.com is cool'],
            [false, 'website。com。'],
            [false, 'website。com'],
            [false, 'website%2Ecom'],
            [false, 'website/./com'],
            [false, '.rn'],
            [false, 'websitecom/a'],
            [false, 'websitecom%2falsea'],
            [false, '`hello'],
            [false, 'hello[my friend]'],
        ];
    }

    public function isEmailDataProvider()
    {
        return [
            [true, 'john.doe@prestashop.com'],
            [true, 'john.doe+alias@prestshop.com'],
            [true, 'john.doe+alias@pr.e.sta.shop.com'],
            [true, 'j@p.com'],
            [true, 'john#doe@prestashop.com'],
            [false, ''],
            [false, 'john.doe@prestashop,com'],
            [false, 'john.doe@prestashop'],
            [true, 'john.doe@сайт.рф'],
            [true, 'john.doe@xn--80aswg.xn--p1ai'],
            [false, 'иван@prestashop.com'], // rfc6531 valid but not cyrillic mailer compatible
            [true, 'xn--80adrw@prestashop.com'],
            [true, 'xn--80adrw@xn--80aswg.xn--p1ai'],
            [false, 123456789],
            [false, false],
        ];
    }

    public function isBirthDateProvider()
    {
        return [
            [true, '1991-04-19'],
            [true, '2015-03-22'],
            [true, '1945-07-25'],
            [false, '3000-03-19'],
            [false, '1991-03-33'],
            [false, '1991-15-19'],
            [false, '1801-01-01'],
            [false, '0085-02-25'],
            [true, date('Y-m-d', strtotime('now'))],
            [true, date('Y-m-d', strtotime('-1 day'))],
            [false, date('Y-m-d', strtotime('+1 day'))],
            [true, date('Y-m-d', strtotime('-1 month'))],
            [true, date('Y-m-d', strtotime('-1 month -1 day'))],
            [true, date('Y-m-d', strtotime('-1 month +1 day'))],
            [false, date('Y-m-d', strtotime('+1 month'))],
            [false, date('Y-m-d', strtotime('+1 month -1 day'))],
            [false, date('Y-m-d', strtotime('+1 month +1 day'))],
            [true, date('Y-m-d', strtotime('-1 year'))],
            [true, date('Y-m-d', strtotime('-1 year -1 day'))],
            [true, date('Y-m-d', strtotime('-1 year +1 day'))],
            [true, date('Y-m-d', strtotime('-1 year -1 month'))],
            [true, date('Y-m-d', strtotime('-1 year -1 month -1 day'))],
            [true, date('Y-m-d', strtotime('-1 year -1 month +1 day'))],
            [true, date('Y-m-d', strtotime('-1 year +1 month'))],
            [true, date('Y-m-d', strtotime('-1 year +1 month -1 day'))],
            [true, date('Y-m-d', strtotime('-1 year +1 month +1 day'))],
            [false, date('Y-m-d', strtotime('+1 year'))],
            [false, date('Y-m-d', strtotime('+1 year -1 day'))],
            [false, date('Y-m-d', strtotime('+1 year +1 day'))],
            [false, date('Y-m-d', strtotime('+1 year -1 month'))],
            [false, date('Y-m-d', strtotime('+1 year -1 month -1 day'))],
            [false, date('Y-m-d', strtotime('+1 year -1 month +1 day'))],
            [false, date('Y-m-d', strtotime('+1 year +1 month'))],
            [false, date('Y-m-d', strtotime('+1 year +1 month -1 day'))],
            [false, date('Y-m-d', strtotime('+1 year +1 month +1 day'))],
            [false, date('Y-m-d', strtotime('-121 year'))],
        ];
    }

    public function isDateOrNullProvider()
    {
        return [
            [true, '1991-04-19'],
            [true, '2015-03-22'],
            [true, '1945-07-25'],
            [true, '2020-03-19'],
            [true, '2020-03-19 10:23:00'],
            [true, '2020-03-19 45:99:99'], // Only the date is actually checked
            [false, '1991-03-33'],
            [false, '1991-03-33 00:50:00'],
            [false, '1991-15-19'],
            [true, null],
            [true, '0000-00-00 00:00:00'],
            [true, '0000-00-00'],
        ];
    }

    public function isOptFloatDataProvider()
    {
        return array_merge(
            $this->trueFloatDataProvider(),
            [
                [true, -12.2151],
                [true, null],
                [true, ''],
            ]
        );
    }

    public function isUnsignedFloatDataProvider()
    {
        return array_merge(
            $this->trueFloatDataProvider(),
            [
                [false, -12.2151],
                [false, -12, 2151],
                [false, '-12.2151'],
                [false, ''],
                [false, 'A'],
                [false, null],
            ]
        );
    }

    public function trueFloatDataProvider()
    {
        return [
            [true, 12],
            [true, 12.2151],
            [true, 12, 2151],
            [true, '12.2151'],
        ];
    }

    public function isFloatDataProvider()
    {
        return array_merge(
            $this->trueFloatDataProvider(),
            [
                [true, -12.2151],
                [true, -12, 2151],
                [true, '-12.2151'],
                [false, ''],
                [false, 'A'],
                [false, null],
            ]
        );
    }

    public function isArrayWithIdsDataProvider(): array
    {
        return [
            [false, 'This is not an array'],
            [false, 42],
            [false, '42'],
            [true, [666]],
            [true, [4, 5, 9, 14]],
            [true, ['2', 5, 4]],
            [true, ['7', '8', '12']],
            [false, []],
            [false, [69, 1, [], 5]],
            [false, [12, 2.5, 14]],
            [false, [-1, 6, 12]],
            [false, ['A', 1, 9]],
            [false, ['+', 666, '+']],
            [false, [0, 2, 253]],
            [false, [0, 0, 0]],
            [false, [45, true, 9]],
        ];
    }

    /**
     * @dataProvider isCleanSVGDataProvider
     */
    public function testIsCleanSVG(bool $expected, string $svg): void
    {
        $this->assertSame($expected, Validate::isCleanSVG($svg));
    }

    public static function isCleanSVGDataProvider(): iterable
    {
        yield 'shape' => [true, '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="blue"/></svg>'];
        yield 'xml declaration and doctype' => [true, '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"><svg xmlns="http://www.w3.org/2000/svg"><path d="M0 0h24v24H0z"/></svg>'];
        yield 'inkscape' => [true, '<svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" xmlns="http://www.w3.org/2000/svg"><sodipodi:namedview id="base" inkscape:zoom="1"/><metadata><rdf:RDF><dc:format>image/svg+xml</dc:format></rdf:RDF></metadata><g inkscape:label="Layer 1" inkscape:groupmode="layer"><rect width="5" height="5"/></g></svg>'];
        yield 'illustrator entities' => [true, '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd" [<!ENTITY ns_extend "http://ns.adobe.com/Extensibility/1.0/"><!ENTITY ns_ai "http://ns.adobe.com/AdobeIllustrator/10.0/">]><svg xmlns:x="&ns_extend;" xmlns:i="&ns_ai;" xmlns="http://www.w3.org/2000/svg"><rect width="10" height="10"/></svg>'];
        yield 'embedded image' => [true, '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><image width="1" height="1" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=="/></svg>'];
        yield 'local use, style and gradient' => [true, '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><style>.a{fill:url(#g)}</style><defs><linearGradient id="g"><stop offset="0" stop-color="red"/></linearGradient><path id="p" d="M0 0L5 5"/></defs><use xlink:href="#p" class="a"/></svg>'];
        yield 'text with references' => [true, '<svg xmlns="http://www.w3.org/2000/svg"><text x="1" y="10">Caf&#233; &amp; bar</text></svg>'];
        yield 'entity with quotes and comment' => [true, '<?xml version="1.0"?><!DOCTYPE svg [<!-- title --><!ENTITY t \'say "hi" > 2\'>]><svg xmlns="http://www.w3.org/2000/svg"><text>&t;</text></svg>'];
        yield 'utf-16 shape' => [true, self::toUtf16('<?xml version="1.0" encoding="UTF-16"?><svg xmlns="http://www.w3.org/2000/svg"><rect width="9" height="9"/></svg>')];

        yield 'empty' => [false, ''];
        yield 'not xml' => [false, '<svg xmlns="http://www.w3.org/2000/svg"><rect></svg>'];
        yield 'script' => [false, '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'];
        yield 'event attribute' => [false, '<svg xmlns="http://www.w3.org/2000/svg"><circle r="40" onload="alert(1)"/></svg>'];
        yield 'javascript link' => [false, '<svg xmlns="http://www.w3.org/2000/svg"><a href="javascript:alert(1)"><rect width="9" height="9"/></a></svg>'];
        yield 'prefixed svg script' => [false, '<svg xmlns="http://www.w3.org/2000/svg" xmlns:s="http://www.w3.org/2000/svg"><s:script>alert(1)</s:script></svg>'];
        yield 'prefixed xhtml script' => [false, '<svg xmlns="http://www.w3.org/2000/svg"><h:script xmlns:h="http://www.w3.org/1999/xhtml">alert(1)</h:script></svg>'];
        yield 'prefixed iframe srcdoc' => [false, '<svg xmlns="http://www.w3.org/2000/svg"><foreignObject width="9" height="9"><h:iframe xmlns:h="http://www.w3.org/1999/xhtml" srcdoc="&lt;script&gt;alert(1)&lt;/script&gt;"/></foreignObject></svg>'];
        yield 'entity script' => [false, '<?xml version="1.0"?><!DOCTYPE svg [<!ENTITY x "&#60;script&#62;alert(1)&#60;/script&#62;">]><svg xmlns="http://www.w3.org/2000/svg">&x;</svg>'];
        yield 'split entity javascript link' => [false, '<?xml version="1.0"?><!DOCTYPE svg [<!ENTITY a "javascr"><!ENTITY b "ipt:alert(1)">]><svg xmlns="http://www.w3.org/2000/svg"><a href="&a;&b;"><rect width="9" height="9"/></a></svg>'];
        yield 'parameter entity' => [false, '<?xml version="1.0"?><!DOCTYPE svg [<!ENTITY % d "<!ENTITY x \'&#38;#60;script&#38;#62;alert(1)&#38;#60;/script&#38;#62;\'>">%d;]><svg xmlns="http://www.w3.org/2000/svg">&x;</svg>'];
        yield 'external entity' => [false, '<?xml version="1.0"?><!DOCTYPE svg [<!ENTITY x SYSTEM "file:///etc/passwd">]><svg xmlns="http://www.w3.org/2000/svg"><text>&x;</text></svg>'];
        yield 'character reference javascript link' => [false, '<svg xmlns="http://www.w3.org/2000/svg"><a href="java&#115;cript:alert(1)"><rect width="9" height="9"/></a></svg>'];
        yield 'character reference colon javascript link' => [false, '<svg xmlns="http://www.w3.org/2000/svg"><a href="javascript&#58;alert(1)"><rect width="9" height="9"/></a></svg>'];
        yield 'tab in javascript link' => [false, '<svg xmlns="http://www.w3.org/2000/svg"><a href="javascript&#9;:alert(1)"><rect width="9" height="9"/></a></svg>'];
        yield 'animated javascript link' => [false, '<svg xmlns="http://www.w3.org/2000/svg"><a><set attributeName="href" to="java&#115;cript:alert(1)"/><rect width="9" height="9"/></a></svg>'];
        yield 'use data url' => [false, '<svg xmlns="http://www.w3.org/2000/svg"><use href="data:image/svg+xml;base64,PHN2Zz48L3N2Zz4=#x"/></svg>'];
        yield 'xslt stylesheet' => [false, '<?xml-stylesheet type="text/xsl" href="evil.svg"?><svg xmlns="http://www.w3.org/2000/svg"><rect width="9" height="9"/></svg>'];
        yield 'default event attribute' => [false, '<?xml version="1.0"?><!DOCTYPE svg [<!ATTLIST svg onload CDATA "alert(1)">]><svg xmlns="http://www.w3.org/2000/svg"><rect width="9" height="9"/></svg>'];
        yield 'default javascript link from entities' => [false, '<?xml version="1.0"?><!DOCTYPE svg [<!ENTITY a "javascr"><!ENTITY b "ipt:alert(1)"><!ATTLIST a href CDATA "&a;&b;">]><svg xmlns="http://www.w3.org/2000/svg"><a><rect width="9" height="9"/></a></svg>'];
        yield 'element declaration' => [false, '<?xml version="1.0"?><!DOCTYPE svg [<!ELEMENT svg ANY>]><svg xmlns="http://www.w3.org/2000/svg"/>'];
        yield 'utf-16 default event attribute' => [false, self::toUtf16('<?xml version="1.0" encoding="UTF-16"?><!DOCTYPE svg [<!ATTLIST svg onload CDATA "alert(1)">]><svg xmlns="http://www.w3.org/2000/svg"><rect width="9" height="9"/></svg>')];
        yield 'utf-16 external entity' => [false, self::toUtf16('<?xml version="1.0" encoding="UTF-16"?><!DOCTYPE svg [<!ENTITY x SYSTEM "file:///etc/passwd">]><svg xmlns="http://www.w3.org/2000/svg"><text>&x;</text></svg>')];
        yield 'utf-16 parameter entity' => [false, self::toUtf16('<?xml version="1.0" encoding="UTF-16"?><!DOCTYPE svg [<!ENTITY % d SYSTEM "file:///etc/passwd">%d;]><svg xmlns="http://www.w3.org/2000/svg"/>')];
    }

    private static function toUtf16(string $xml): string
    {
        return "\xFF\xFE" . mb_convert_encoding($xml, 'UTF-16LE', 'UTF-8');
    }
}
