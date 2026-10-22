<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Form\ChoiceProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\GroupByIdChoiceProvider;

class GroupByIdChoiceProviderTest extends TestCase
{
    private const LANG_ID = 1;

    /**
     * @var array<int, array<string, int|string>>
     */
    private $groups = [
        ['id_group' => 1, 'name' => 'Visitor'],
        ['id_group' => 2, 'name' => 'Guest'],
        ['id_group' => 3, 'name' => 'Customer'],
    ];

    public function testItProvidesAllGroupsWithoutShopFilteringByDefault(): void
    {
        $groupDataProvider = $this->createMock(GroupDataProvider::class);
        $groupDataProvider
            ->expects($this->once())
            ->method('getGroups')
            ->with(self::LANG_ID, false)
            ->willReturn($this->groups);

        $choiceProvider = new GroupByIdChoiceProvider($groupDataProvider, self::LANG_ID);

        $this->assertSame(
            ['Visitor' => 1, 'Guest' => 2, 'Customer' => 3],
            $choiceProvider->getChoices()
        );
    }

    public function testItFiltersGroupsByCurrentShopWhenEnabled(): void
    {
        $groupDataProvider = $this->createMock(GroupDataProvider::class);
        $groupDataProvider
            ->expects($this->once())
            ->method('getGroups')
            ->with(self::LANG_ID, true)
            ->willReturn($this->groups);

        $choiceProvider = new GroupByIdChoiceProvider($groupDataProvider, self::LANG_ID, true);

        $this->assertSame(
            ['Visitor' => 1, 'Guest' => 2, 'Customer' => 3],
            $choiceProvider->getChoices()
        );
    }
}
