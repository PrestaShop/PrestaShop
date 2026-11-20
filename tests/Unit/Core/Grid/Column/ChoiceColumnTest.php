<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Column;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ChoiceColumn;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class ChoiceColumnTest extends TestCase
{
    public function testSearchIsDisabledByDefault(): void
    {
        $column = (new ChoiceColumn('status'))->setOptions($this->getRequiredOptions());

        $this->assertFalse($column->getOptions()['searchable']);
    }

    public function testSearchCanBeEnabled(): void
    {
        $column = (new ChoiceColumn('status'))->setOptions($this->getRequiredOptions() + ['searchable' => true]);

        $this->assertTrue($column->getOptions()['searchable']);
    }

    public function testSearchableMustBeABoolean(): void
    {
        $this->expectException(InvalidOptionsException::class);

        (new ChoiceColumn('status'))->setOptions($this->getRequiredOptions() + ['searchable' => 'yes']);
    }

    private function getRequiredOptions(): array
    {
        return [
            'choice_provider' => $this->createMock(ConfigurableFormChoiceProviderInterface::class),
            'field' => 'current_state',
            'route' => 'admin_orders_list_update_status',
        ];
    }
}
