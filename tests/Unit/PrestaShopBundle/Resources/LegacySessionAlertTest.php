<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Resources;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

/**
 * Legacy admin controllers put HTML in their messages, typically a link to the page that fixes the
 * problem being reported. Before PrestaShop 9 those messages were rendered by Smarty templates that
 * never escaped them; they are now rendered by this component, so every channel has to go through
 * raw_purified, which keeps the markup while running it past HTMLPurifier.
 */
class LegacySessionAlertTest extends TestCase
{
    private const TEMPLATE = 'Admin/Component/LegacyLayout/session_alert.html.twig';
    private const LINK = '<a href="https://www.prestashop-project.org">a link</a>';

    /**
     * @dataProvider getMessageChannels
     */
    public function testTheMessageKeepsItsMarkup(string $channel, mixed $value): void
    {
        $html = $this->render([$channel => $value]);

        $this->assertStringContainsString(self::LINK, $html, sprintf('%s should keep its markup', $channel));
        $this->assertStringNotContainsString('&lt;a href', $html, sprintf('%s should not be escaped', $channel));
    }

    public function getMessageChannels(): iterable
    {
        $message = 'Message - ' . self::LINK;

        yield 'confirmationMessage' => ['confirmationMessage', $message];
        yield 'errorMessage' => ['errorMessage', $message];
        yield 'single error' => ['errors', [$message]];
        yield 'several errors' => ['errors', [$message, $message]];
        yield 'confirmations' => ['confirmations', [$message]];
        // Already rendered unescaped before this test existed, kept so a regression is caught.
        yield 'informations' => ['informations', [$message]];
        yield 'warnings' => ['warnings', [$message]];
    }

    /**
     * @param array<string, mixed> $channels
     */
    private function render(array $channels): string
    {
        $twig = new Environment(new FilesystemLoader(__DIR__ . '/../../../../src/PrestaShopBundle/Resources/views'));
        $twig->addFilter(new TwigFilter(
            'trans',
            static fn (string $message, array $parameters = [], ?string $domain = null): string => strtr($message, array_map('strval', $parameters))
        ));
        $twig->addFilter(new TwigFilter(
            'raw_purified',
            static fn (?string $value): string => (string) $value,
            ['is_safe' => ['all']]
        ));

        $controller = new class($channels) {
            public mixed $confirmationMessage = null;
            public mixed $errorMessage = null;
            public array $errors = [];
            public array $informations = [];
            public array $confirmations = [];
            public array $warnings = [];

            /**
             * @param array<string, mixed> $channels
             */
            public function __construct(array $channels)
            {
                foreach ($channels as $name => $value) {
                    $this->{$name} = $value;
                }
            }
        };

        return $twig->render(self::TEMPLATE, ['this' => $controller]);
    }
}
