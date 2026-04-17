<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
