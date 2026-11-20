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

        // Crawlers missing from the former name list
        yield 'OAI-SearchBot' => ['Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko); compatible; OAI-SearchBot/1.0; +https://openai.com/searchbot'];
        yield 'ChatGPT-User' => ['Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko); compatible; ChatGPT-User/1.0; +https://openai.com/bot'];
        yield 'meta-externalagent' => ['meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler)'];
        yield 'Qwantbot' => ['Mozilla/5.0 (compatible; Qwantbot/1.0; +https://help.qwant.com/bot/)'];
        yield 'YandexImages' => ['Mozilla/5.0 (compatible; YandexImages/3.0; +http://yandex.com/bots)'];
        yield 'Barkrowler' => ['Mozilla/5.0 (compatible; Barkrowler/0.9; +https://babbar.tech/crawler)'];
        yield 'Googlebot smartphone' => ['Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'];
        yield 'Storebot-Google smartphone' => ['Mozilla/5.0 (Linux; Android 8.0; Pixel 2 Build/OPD3.170816.012; Storebot-Google/1.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Mobile Safari/537.36'];
        yield 'Mediapartners-Google' => ['Mediapartners-Google'];
        yield 'GoogleOther' => ['Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GoogleOther) Chrome/141.0.7390.54 Safari/537.36'];
        yield 'facebookexternalhit without URL' => ['facebookexternalhit/1.1'];
        yield 'WhatsApp link preview' => ['WhatsApp/2.23.20.0'];
        yield 'WhatsApp link preview with platform' => ['WhatsApp/2.24.20.89 A'];

        // HTTP libraries
        yield 'python-requests' => ['python-requests/2.31.0'];
        yield 'curl' => ['curl/8.4.0'];
        yield 'PycURL' => ['PycURL/7.45.2 libcurl/8.4.0 OpenSSL/3.0.11'];
        yield 'Go-http-client' => ['Go-http-client/1.1'];
        yield 'Scrapy' => ['Scrapy/2.11.0 (+https://scrapy.org)'];
        yield 'Apache-HttpClient' => ['Apache-HttpClient/4.5.14 (Java/17.0.8)'];

        // Truncated or forged browser User-Agents
        yield 'Mozilla without engine' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64)'];
        yield 'bare Mozilla' => ['Mozilla/5.0'];
        yield 'no product token' => ['Mnogosearch-3.1.21'];

        yield from self::readUserAgents('bots.txt');
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
        yield 'HTC Desire' => ['Mozilla/5.0 (Linux; Android 6.0; HTC Desire 626 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36'];
        yield 'Cubot phone' => ['Mozilla/5.0 (Linux; Android 12; CUBOT KINGKONG 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36'];
        yield 'Yandex Browser on Cubot' => ['Mozilla/5.0 (Linux; arm_64; Android 14; CUBOT KINGKONG 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 YaBrowser/25.8.2.42.00 SA/3 Mobile Safari/537.36'];
        yield 'Facebook in-app browser on Cubot' => ['Mozilla/5.0 (Linux; Android 12; KINGKONG 5 Pro Build/SP1A.210812.016; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/139.0.0.0 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/480.0.0.40.110;FBMF/CUBOT;FBBD/CUBOT;FBDV/KINGKONG 5 Pro;FBSV/12;FBOP/1;FBCA/arm64-v8a:;]'];
        yield 'B BOT phone' => ['Mozilla/5.0 (Linux; Android 6.0; B BOT 550 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.124 Mobile Safari/537.36'];
        yield 'TECNO phone' => ['Mozilla/5.0 (Linux; U; Android 4.3; zh-cn; TECNO H3 Build/ AppleWebKit/534.30 (KHTML, like Gecko) Version/4.4.2 Mobile Safari/534.30'];
        yield 'Samsung Internet' => ['Mozilla/5.0 (Linux; Android 13; SM-S911B) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/23.0 Chrome/115.0.0.0 Mobile Safari/537.36'];
        yield 'Safari on iPhone' => ['Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1'];
        yield 'Lynx text browser' => ['Lynx/2.9.0dev.12 libwww-FM/2.14 SSL-MM/1.4.1 GNUTLS/3.8.3'];
        yield 'app token containing "curl"' => ['Mozilla/5.0 (Linux; Android 14; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36 Securly'];
        yield 'Links text browser' => ['Links (2.29; Linux 6.1.0-13-amd64 x86_64; GNU C 12.2; text)'];
        yield 'MAUI WAP feature phone' => ['Maui_Browser_Karbonn_K9'];
        // Merchants' and PrestaShop's own end-to-end tests drive headless browsers through the checkout
        yield 'Headless Chrome' => ['Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/141.0.0.0 Safari/537.36'];
        yield 'empty string' => [''];

        yield from self::readUserAgents('browsers.txt');
    }

    private static function readUserAgents(string $file): iterable
    {
        $lines = file(dirname(__DIR__) . '/Resources/user-agents/' . $file, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $index => $userAgent) {
            if ($userAgent !== '' && $userAgent[0] !== '#') {
                yield $file . ':' . ($index + 1) => [$userAgent];
            }
        }
    }
}
