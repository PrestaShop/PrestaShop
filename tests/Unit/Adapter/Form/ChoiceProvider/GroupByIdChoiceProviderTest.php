<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\Form\ChoiceProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\GroupByIdChoiceProvider;
use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;

class GroupByIdChoiceProviderTest extends TestCase
{
    private const LANG_ID = 1;

    /**
     * Groups as returned by the data provider (ids are strings, like the DB returns them).
     * The first two are the special "unidentified" (Visitor) and "guest" groups.
     *
     * @var array<int, array<string, string>>
     */
    private $groups = [
        ['id_group' => '1', 'name' => 'Visitor'],
        ['id_group' => '2', 'name' => 'Guest'],
        ['id_group' => '3', 'name' => 'Customer'],
    ];

    /**
     * Every group must be selectable, including the unidentified (Visitor) and guest
     * groups: they are needed e.g. when editing a guest customer.
     */
    public function testItProvidesAllGroupsIncludingUnidentifiedAndGuest(): void
    {
        $choiceProvider = new GroupByIdChoiceProvider(
            $this->mockGroupDataProvider(),
            self::LANG_ID
        );

        $this->assertSame(
            ['Visitor' => 1, 'Guest' => 2, 'Customer' => 3],
            $choiceProvider->getChoices()
        );
    }

    private function mockGroupDataProvider(): GroupDataProvider
    {
        $groupDataProvider = $this->createMock(GroupDataProvider::class);
        $groupDataProvider
            ->expects($this->once())
            ->method('getGroups')
            ->with(self::LANG_ID, true)
            ->willReturn($this->groups);

        return $groupDataProvider;
    }
}
