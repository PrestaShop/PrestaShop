<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;

class FormChoiceFormatterTest extends TestCase
{
    /**
     * @dataProvider getFormOptionsToFormat
     *
     * @param array $rawOptions
     * @param string $idKey
     * @param string $nameKey
     * @param bool $sortByName
     * @param array $expectedFormattedChoices
     */
    public function testFormatFormChoices(array $rawOptions, string $idKey, string $nameKey, bool $sortByName, array $expectedFormattedChoices): void
    {
        $returnedFormattedChoices = FormChoiceFormatter::formatFormChoices($rawOptions, $idKey, $nameKey, $sortByName);
        $this->assertEquals($expectedFormattedChoices, $returnedFormattedChoices);
    }

    public function getFormOptionsToFormat(): iterable
    {
        yield 'manufacturer list with duplicates' => [
            [
                [
                    'id_manufacturer' => 1,
                    'name' => 'Krystian and son development',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 2,
                    'name' => 'Preston Manufacturing',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 3,
                    'name' => 'Trendo',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 4,
                    'name' => 'Hiba Manufacturing',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 5,
                    'name' => 'Krystian and son development',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 6,
                    'name' => 'Daniel Manufacturing',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 7,
                    'name' => 'Krystian and son development',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 8,
                    'name' => 'Hiba Manufacturing',
                    'someproperty' => 'somevalue',
                ],
            ],
            'id_manufacturer',
            'name',
            true,
            [
                'Daniel Manufacturing' => 6,
                'Hiba Manufacturing (4)' => 4,
                'Hiba Manufacturing (8)' => 8,
                'Krystian and son development (1)' => 1,
                'Krystian and son development (5)' => 5,
                'Krystian and son development (7)' => 7,
                'Preston Manufacturing' => 2,
                'Trendo' => 3,
            ],
        ];

        yield 'manufacturer list without duplicates' => [
            [
                [
                    'id_manufacturer' => 1,
                    'name' => 'Krystian and son development',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 2,
                    'name' => 'Preston Manufacturing',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 3,
                    'name' => 'Trendo',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 4,
                    'name' => 'Hiba Manufacturing',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 6,
                    'name' => 'Daniel Manufacturing',
                    'someproperty' => 'somevalue',
                ],
            ],
            'id_manufacturer',
            'name',
            true,
            [
                'Daniel Manufacturing' => 6,
                'Hiba Manufacturing' => 4,
                'Krystian and son development' => 1,
                'Preston Manufacturing' => 2,
                'Trendo' => 3,
            ],
        ];

        yield 'manufacturer list with duplicates and disabled sorting' => [
            [
                [
                    'id_manufacturer' => 1,
                    'name' => 'Krystian and son development',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 2,
                    'name' => 'Preston Manufacturing',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 3,
                    'name' => 'Trendo',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 4,
                    'name' => 'Hiba Manufacturing',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 5,
                    'name' => 'Krystian and son development',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 6,
                    'name' => 'Daniel Manufacturing',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 7,
                    'name' => 'Krystian and son development',
                    'someproperty' => 'somevalue',
                ],
                [
                    'id_manufacturer' => 8,
                    'name' => 'Hiba Manufacturing',
                    'someproperty' => 'somevalue',
                ],
            ],
            'id_manufacturer',
            'name',
            false,
            [
                'Krystian and son development (1)' => 1,
                'Preston Manufacturing' => 2,
                'Trendo' => 3,
                'Hiba Manufacturing (4)' => 4,
                'Krystian and son development (5)' => 5,
                'Daniel Manufacturing' => 6,
                'Krystian and son development (7)' => 7,
                'Hiba Manufacturing (8)' => 8,
            ],
        ];
    }
}
