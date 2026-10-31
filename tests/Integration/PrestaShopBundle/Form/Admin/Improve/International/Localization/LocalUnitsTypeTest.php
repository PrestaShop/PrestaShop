<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShopBundle\Form\Admin\Improve\International\Localization\LocalUnitsType;
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;

class LocalUnitsTypeTest extends FormListenerTestCase
{
    /**
     * Deliberately a literal and not LocalUnitsType::MAX_UNIT_LENGTH: the test has to keep loading
     * against a build without the constant, otherwise reverting the fix produces an "undefined constant"
     * error instead of a failing assertion.
     */
    private const MAX_UNIT_LENGTH = 20;

    /**
     * @dataProvider getUnitFieldNames
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/28910
     */
    public function testAUnitLongerThanTheLimitIsRejected(string $fieldName): void
    {
        $form = $this->createForm(LocalUnitsType::class, ['csrf_protection' => false]);
        $form->submit($this->submittedUnits([$fieldName => str_repeat('a', self::MAX_UNIT_LENGTH + 1)]));

        $this->assertFalse($form->isValid());
        $this->assertGreaterThan(0, $form->get($fieldName)->getErrors()->count());
    }

    /**
     * @dataProvider getUnitFieldNames
     */
    public function testAUnitAtTheLimitIsAccepted(string $fieldName): void
    {
        $form = $this->createForm(LocalUnitsType::class, ['csrf_protection' => false]);
        $form->submit($this->submittedUnits([$fieldName => str_repeat('a', self::MAX_UNIT_LENGTH)]));

        $this->assertTrue($form->isValid(), (string) $form->getErrors(true, false));
    }

    public function getUnitFieldNames(): iterable
    {
        yield ['weight_unit'];
        yield ['distance_unit'];
        yield ['volume_unit'];
        yield ['dimension_unit'];
    }

    /**
     * @param array<string, string> $overrides
     *
     * @return array<string, string>
     */
    private function submittedUnits(array $overrides): array
    {
        return array_merge([
            'weight_unit' => 'kg',
            'distance_unit' => 'km',
            'volume_unit' => 'L',
            'dimension_unit' => 'cm',
        ], $overrides);
    }
}
