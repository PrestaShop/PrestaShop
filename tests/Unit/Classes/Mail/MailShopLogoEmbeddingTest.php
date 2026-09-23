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

/**
 * The logo was attached to every message as soon as one was configured, so a template that does not
 * display it still carried the image and mail clients showed it as an attachment. It is now attached
 * only when the html body has somewhere to show it.
 *
 * All 34 html templates PrestaShop ships do reference it, through the shared header component, so
 * this changes nothing for them; it is custom and module templates that stop carrying the file.
 */
class MailShopLogoEmbeddingTest extends TestCase
{
    /**
     * @dataProvider bodies
     *
     * @param mixed $templateHtml
     */
    public function testTheLogoIsAttachedOnlyWhenTheBodyCanShowIt($templateHtml, bool $expected): void
    {
        $method = new ReflectionMethod(Mail::class, 'templateShowsShopLogo');
        $method->setAccessible(true);

        $this->assertSame($expected, $method->invoke(null, $templateHtml));
    }

    public static function bodies(): iterable
    {
        yield 'the placeholder, as the shipped header component uses it' => [
            '<table><tr><td><img src="{shop_logo}" alt=""></td></tr></table>', true,
        ];
        yield 'the content id written directly' => [
            '<img src="cid:shop_logo" alt="">', true,
        ];
        yield 'the placeholder anywhere in the body' => [
            "<p>hello</p>\n<div>{shop_logo}</div>", true,
        ];
        yield 'a body that never shows it' => [
            '<p>Your order has shipped.</p>', false,
        ];
        yield 'another placeholder is not this one' => [
            '<p>{shop_name} says hello</p>', false,
        ];
        // Reached when the html file is missing, since $templateHtml starts empty.
        yield 'an empty body' => ['', false];
        yield 'not a string at all' => [null, false];
    }

    public function testTheShippedHeaderComponentStillAsksForTheLogo(): void
    {
        $method = new ReflectionMethod(Mail::class, 'templateShowsShopLogo');
        $method->setAccessible(true);

        foreach (['classic', 'modern'] as $theme) {
            $header = _PS_ROOT_DIR_ . '/mails/themes/' . $theme . '/components/header.html.twig';
            $this->assertFileExists($header);
            $this->assertTrue(
                $method->invoke(null, (string) file_get_contents($header)),
                sprintf('The %s header component should still reference the shop logo.', $theme)
            );
        }
    }
}
