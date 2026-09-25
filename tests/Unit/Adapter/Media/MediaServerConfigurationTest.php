<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Media;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Media\MediaServerConfiguration;
use PrestaShop\PrestaShop\Adapter\Tools;

class MediaServerConfigurationTest extends TestCase
{
    /**
     * @var Configuration|MockObject
     */
    private $configuration;

    /**
     * @var Tools|MockObject
     */
    private $tools;

    protected function setUp(): void
    {
        $this->configuration = $this->createMock(Configuration::class);
        $this->tools = $this->createMock(Tools::class);
    }

    public function testItRewritesHtaccessWhenAServerChanges(): void
    {
        $this->givenStoredServers('', '', '');
        $this->tools->expects($this->once())->method('generateHtaccess')->willReturn(true);

        $errors = $this->configurator()->updateConfiguration($this->submit('localhost'));

        $this->assertSame([], $errors);
    }

    /**
     * Saving the form without touching the fields must not rewrite the file: the page
     * is shared with other settings, so an unchanged save would rewrite .htaccess on
     * every visit that presses Save.
     */
    public function testItLeavesHtaccessAloneWhenNothingChanged(): void
    {
        $this->givenStoredServers('localhost', '', '');
        $this->tools->expects($this->never())->method('generateHtaccess');

        $errors = $this->configurator()->updateConfiguration($this->submit('localhost'));

        $this->assertSame([], $errors);
    }

    public function testItReportsAnUnwritableHtaccess(): void
    {
        $this->givenStoredServers('', '', '');
        $this->tools->method('generateHtaccess')->willReturn(false);

        $errors = $this->configurator()->updateConfiguration($this->submit('localhost'));

        $this->assertCount(1, $errors);
        $this->assertSame('Admin.Advparameters.Notification', $errors[0]['domain']);
    }

    /**
     * An invalid value never reaches the configuration, so there is nothing to
     * regenerate either.
     */
    public function testItDoesNotRewriteHtaccessWhenTheValueIsRejected(): void
    {
        $this->tools->expects($this->never())->method('generateHtaccess');
        $this->configuration->expects($this->never())->method('set');

        $errors = $this->configurator()->updateConfiguration($this->submit('not a domain at all'));

        $this->assertNotEmpty($errors);
    }

    private function configurator(): MediaServerConfiguration
    {
        return new MediaServerConfiguration($this->configuration, $this->tools);
    }

    /**
     * `localhost` rather than a realistic CDN name on purpose: the class validates a
     * media server with `gethostbyname()`, so any name used here has to resolve, and a
     * unit test must not depend on the machine having DNS.
     */
    private function submit(string $one, string $two = '', string $three = ''): array
    {
        return [
            'media_server_one' => $one,
            'media_server_two' => $two,
            'media_server_three' => $three,
        ];
    }

    private function givenStoredServers(string $one, string $two, string $three): void
    {
        // A callback rather than willReturnMap: the map matches on the full argument
        // list, and Configuration::get() carries optional parameters, so the entry has
        // to mirror the call shape exactly to match. The callback does not care.
        $stored = [
            'PS_MEDIA_SERVER_1' => $one,
            'PS_MEDIA_SERVER_2' => $two,
            'PS_MEDIA_SERVER_3' => $three,
        ];
        $this->configuration->method('get')->willReturnCallback(
            static fn ($key) => $stored[$key] ?? null
        );
    }
}
