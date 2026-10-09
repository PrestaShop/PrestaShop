<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Form\ChoiceProvider;

use Db;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\OrderCountriesChoiceProvider;

/**
 * The orders grid definition asks this provider for its choices twice while building a single page:
 * once to decide whether to add the "Delivery" column, once to fill the country filter. The query
 * behind it has to look at every order, so the second round trip is pure waste.
 */
class OrderCountriesChoiceProviderTest extends TestCase
{
    /**
     * @var array<string> SQL of every query issued through the mocked Db
     */
    private $executedQueries = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->executedQueries = [];

        $mockDatabase = $this->createMock(Db::class);
        $mockDatabase->method('getValue')->willReturnCallback(function ($sql) {
            $this->executedQueries[] = (string) $sql;

            return 1;
        });
        $mockDatabase->method('executeS')->willReturnCallback(function ($sql) {
            $this->executedQueries[] = (string) $sql;

            return [
                ['id_country' => 8, 'name' => 'France'],
                ['id_country' => 21, 'name' => 'United States'],
            ];
        });

        Db::setInstanceForTesting($mockDatabase);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Db::deleteTestingInstance();
    }

    public function testTheChoicesAreOnlyQueriedOnce(): void
    {
        $provider = new OrderCountriesChoiceProvider();

        $first = $provider->getChoices();
        $queriesAfterFirstCall = count($this->executedQueries);

        $second = $provider->getChoices();

        $this->assertGreaterThan(0, $queriesAfterFirstCall, 'the first call must really hit the database, or this test proves nothing');
        $this->assertCount($queriesAfterFirstCall, $this->executedQueries, 'the second call must not query again');
        $this->assertSame($first, $second);
        $this->assertSame(['France' => 8, 'United States' => 21], $first);
    }

    /**
     * The empty answer is the one a shop with no orders gets, and it is also the one the grid uses to
     * decide the column is not worth showing, so it has to be remembered too.
     */
    public function testAnEmptyResultIsAlsoRemembered(): void
    {
        $mockDatabase = $this->createMock(Db::class);
        $mockDatabase->method('getValue')->willReturnCallback(function ($sql) {
            $this->executedQueries[] = (string) $sql;

            return false;
        });
        $mockDatabase->method('executeS')->willReturnCallback(function ($sql) {
            $this->executedQueries[] = (string) $sql;

            return [];
        });
        Db::setInstanceForTesting($mockDatabase);

        $provider = new OrderCountriesChoiceProvider();

        $this->assertSame([], $provider->getChoices());
        $queriesAfterFirstCall = count($this->executedQueries);
        $this->assertSame(1, $queriesAfterFirstCall, 'an unused country table short circuits before the expensive query');

        $this->assertSame([], $provider->getChoices());
        $this->assertCount($queriesAfterFirstCall, $this->executedQueries, 'the second call must not query again');
    }
}
