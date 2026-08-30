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
        yield 'empty string' => [''];
    }
}
