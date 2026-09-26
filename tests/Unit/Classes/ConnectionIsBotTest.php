<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Classes;

use Connection;
use PHPUnit\Framework\TestCase;

class ConnectionIsBotTest extends TestCase
{
    /**
     * @dataProvider providesBotUserAgents
     */
    public function testIsBotReturnsTrueForBots(string $userAgent): void
    {
        $this->assertTrue(Connection::isBot($userAgent));
    }

    /**
     * @dataProvider providesHumanUserAgents
     */
    public function testIsBotReturnsFalseForHumans(string $userAgent): void
    {
        $this->assertFalse(Connection::isBot($userAgent));
    }

    public function testIsBotReturnsFalseWhenNoUserAgent(): void
    {
        $this->assertFalse(Connection::isBot());
    }

    public static function providesBotUserAgents(): iterable
    {
        // Legacy bots from original regex
        yield 'googlebot' => ['Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'];
        yield 'msnbot' => ['msnbot/2.0b (+http://search.msn.com/msnbot.htm)'];
        yield 'YandexBot' => ['Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)'];
        yield 'Motor' => ['Motor/0.2 libwww-perl/5.64'];
        yield 'webs' => ['webs@recruit.co.jp'];

        // Search engine crawlers
        yield 'bingbot' => ['Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'];
        yield 'Baiduspider' => ['Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)'];
        yield 'Applebot' => ['Mozilla/5.0 (compatible; Applebot/0.3; +http://www.apple.com/go/applebot)'];
        yield 'Amazonbot' => ['Mozilla/5.0 (compatible; Amazonbot/0.1; +https://developer.amazon.com/support/amazonbot)'];

        // SEO tools crawlers
        yield 'AhrefsBot' => ['Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)'];
        yield 'SemrushBot' => ['Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)'];
        yield 'DotBot' => ['Mozilla/5.0 (compatible; DotBot/1.2; +https://opensiteexplorer.org/dotbot; help@moz.com)'];
        yield 'MJ12bot' => ['Mozilla/5.0 (compatible; MJ12bot/v1.4.8; http://mj12bot.com/)'];
        yield 'PetalBot' => ['Mozilla/5.0 (compatible; PetalBot; +https://aspiegel.com/petalbot)'];
        yield 'DataForSeoBot' => ['Mozilla/5.0 (compatible; DataForSeoBot/1.0; +https://dataforseo.com/dataforseo-bot)'];

        // AI crawlers
        yield 'GPTBot' => ['Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.1; +https://openai.com/gptbot)'];
        yield 'ClaudeBot' => ['Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; ClaudeBot/1.0; +claudebot@anthropic.com)'];
        yield 'CCBot' => ['CCBot/2.0 (https://commoncrawl.org/faq/)'];
        yield 'Bytespider' => ['Mozilla/5.0 (Linux; Android 5.0) AppleWebKit/537.36 (KHTML, like Gecko) Mobile Safari/537.36 (compatible; Bytespider; spider-feedback@bytedance.com)'];
        yield 'PerplexityBot' => ['Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; PerplexityBot/1.0; +https://perplexity.ai/perplexitybot)'];

        // Other major crawlers
        yield 'DuckDuckBot' => ['DuckDuckBot/1.0; (+http://duckduckgo.com/duckduckbot.html)'];
        yield 'ia_archiver' => ['ia_archiver (+http://www.alexa.com/site/help/webmaster; crawler@alexa.com)'];

        // Social media crawlers
        yield 'facebookexternalhit' => ['facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)'];
        yield 'Twitterbot' => ['Twitterbot/1.0'];
        yield 'LinkedInBot' => ['LinkedInBot/1.0 (compatible; Mozilla/5.0; Apache-HttpClient +http://www.linkedin.com)'];
        yield 'Slackbot' => ['Slackbot-LinkExpanding 1.0 (+https://api.slack.com/robots)'];
        yield 'Discordbot' => ['Mozilla/5.0 (compatible; Discordbot/2.0; +https://discordapp.com)'];
        yield 'TelegramBot' => ['TelegramBot (like TwitterBot)'];

        // Performance & audit tools
        yield 'lighthouse' => ['Mozilla/5.0 (Linux; Android 11; moto g power (2022)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Mobile Safari/537.36 Chrome-Lighthouse'];
        yield 'PageSpeed' => ['Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.6045.105 Safari/537.36 PageSpeed'];
    }

    public static function providesHumanUserAgents(): iterable
    {
        yield 'Chrome on Windows' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36'];
        yield 'Firefox on Linux' => ['Mozilla/5.0 (X11; Linux x86_64; rv:125.0) Gecko/20100101 Firefox/125.0'];
        yield 'Safari on macOS' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 14_4_1) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4.1 Safari/605.1.15'];
        yield 'Mobile Chrome on Android' => ['Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36'];
        yield 'Instagram in-app browser on Motorola' => ['Mozilla/5.0 (Linux; Android 16; moto g77 Build/W2WIS36.43-92-1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/153.0.8010.26 Mobile Safari/537.36 Instagram 446.0.0.49.77 Android (36/16; 390dpi; 1080x2352; motorola; moto g77; naples; mt6835; it_IT; 1061744266; IABMV/1)'];
        yield 'Facebook in-app browser on Motorola' => ['Mozilla/5.0 (Linux; Android 14; moto g54 5G Build/U1TDS34.94-12-9-10; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/124.0.6367.179 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/464.0.0.46.101;FBBV/593823425;FBDM/{density=2.0,width=720,height=1472};FBLC/it_IT;FBRV/0;FBCR/;FBMF/motorola;FBBD/motorola;FBPN/com.facebook.katana;FBDV/moto g54 5G;FBSV/14;FBOP/1;FBCA/arm64-v8a:;]'];
        yield 'WeChat in-app browser' => ['Mozilla/5.0 (Linux; Android 13; V2227A Build/TP1A.220624.014; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/111.0.5563.116 Mobile Safari/537.36 XWEB/1110017 MMWEBSDK/20230805 MMWEBID/2580 MicroMessenger/8.0.42.2460(0x28002A3B) WeChat/arm64 Weixin NetType/WIFI Language/zh_CN ABI/arm64'];
        yield 'empty string' => [''];
    }
}
