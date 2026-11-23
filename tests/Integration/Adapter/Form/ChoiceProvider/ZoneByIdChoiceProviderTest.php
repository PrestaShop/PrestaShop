<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Form\ChoiceProvider;

use Cache;
use Db;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\ZoneByIdChoiceProvider;
use Symfony\Contracts\Translation\TranslatorInterface;

class ZoneByIdChoiceProviderTest extends TestCase
{
    private int $zoneId;

    private string $zoneName;

    protected function setUp(): void
    {
        parent::setUp();

        $zone = Db::getInstance()->getRow('SELECT id_zone, name FROM ' . _DB_PREFIX_ . 'zone WHERE active = 1 ORDER BY id_zone');
        $this->zoneId = (int) $zone['id_zone'];
        $this->zoneName = $zone['name'];
    }

    protected function tearDown(): void
    {
        $this->setZoneActive(true);

        parent::tearDown();
    }

    public function testAnInactiveZoneIsMarkedWhenAsked(): void
    {
        $this->setZoneActive(false);

        $choices = $this->createProvider()->getChoices(['mark_inactive' => true]);

        self::assertSame($this->zoneId, $choices[$this->zoneName . ' (Translated inactive)'] ?? null);
        self::assertArrayNotHasKey($this->zoneName, $choices);
    }

    public function testAnActiveZoneIsNotMarked(): void
    {
        $choices = $this->createProvider()->getChoices(['mark_inactive' => true]);

        self::assertSame($this->zoneId, $choices[$this->zoneName] ?? null);
    }

    public function testZonesAreNotMarkedByDefault(): void
    {
        $this->setZoneActive(false);

        $choices = $this->createProvider()->getChoices([]);

        self::assertSame($this->zoneId, $choices[$this->zoneName] ?? null);
    }

    private function createProvider(): ZoneByIdChoiceProvider
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->with('Inactive', [], 'Admin.Global')->willReturn('Translated inactive');

        return new ZoneByIdChoiceProvider($translator);
    }

    private function setZoneActive(bool $active): void
    {
        Db::getInstance()->update('zone', ['active' => (int) $active], 'id_zone = ' . $this->zoneId);
        Cache::clean('Zone::getZones_*');
    }
}
