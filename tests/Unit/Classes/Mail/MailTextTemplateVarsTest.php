<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Mail;

use Mail;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Tools;

class MailTextTemplateVarsTest extends TestCase
{
    /**
     * @dataProvider shopNames
     */
    public function testTheTextPartShowsTheShopNameUnescaped(string $shopName): void
    {
        $textVars = $this->getTextTemplateVars([
            '{shop_name}' => Tools::safeOutput($shopName),
            '{firstname}' => 'Joe &amp; co',
        ]);

        $this->assertSame($shopName, $textVars['{shop_name}']);
        $this->assertSame('Joe &amp; co', $textVars['{firstname}'], 'Only the shop name is escaped by Mail::send()');
    }

    public static function shopNames(): iterable
    {
        yield 'apostrophe and ampersand' => ["Joe's Pizza & Grill"];
        yield 'quotes and greater-than' => ['"Shop" > others'];
        yield 'accents' => ['Boulangerie Élise'];
        yield 'plain' => ['My shop'];
    }

    public function testAMissingShopNameIsLeftAlone(): void
    {
        $this->assertSame(['{order_name}' => 'XKBKNABJK'], $this->getTextTemplateVars(['{order_name}' => 'XKBKNABJK']));
    }

    private function getTextTemplateVars(array $templateVars): array
    {
        return (new ReflectionMethod(Mail::class, 'getTextTemplateVars'))->invoke(null, $templateVars);
    }
}
