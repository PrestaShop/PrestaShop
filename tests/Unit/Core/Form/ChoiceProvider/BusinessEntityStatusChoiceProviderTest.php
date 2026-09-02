<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\BusinessEntityStatusChoiceProvider;

class BusinessEntityStatusChoiceProviderTest extends ChoiceProviderTestCase
{
    /**
     * The orientation matters: ChoiceType expects label => value, and inverting it would render the
     * raw enum values in the status filter of the grid.
     */
    public function testItProvidesOneTranslatedChoicePerStatus(): void
    {
        $choiceProvider = new BusinessEntityStatusChoiceProvider($this->mockTranslator());

        $this->assertSame([
            'Pending' => 'pending',
            'Active' => 'active',
            'Inactive' => 'inactive',
            'Rejected' => 'rejected',
        ], $choiceProvider->getChoices(), 'a new status must not be silently missing from the grid filter');
    }
}
