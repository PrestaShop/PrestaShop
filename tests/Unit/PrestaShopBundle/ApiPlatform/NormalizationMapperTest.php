<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\ApiPlatform;

use PHPUnit\Framework\TestCase;

class NormalizationMapperTest extends TestCase
{
    /**
     * @dataProvider getDataToMap
     */
    public function testNormalize(array $normalizedData, array $mapping, $expectedData): void
    {
        $normalizer = new NormalizationMapper();
        $context = [NormalizationMapper::NORMALIZATION_MAPPING => $mapping];
        $normalizer->mapNormalizedData($normalizedData, $context);
        $this->assertEquals($expectedData, $normalizedData);
    }

    public function getDataToMap(): iterable
    {
        yield 'handle renaming of field names' => [
            [
                'isActive' => true,
                'names' => [
                    'en-US' => 'name',
                    'fr-FR' => 'nom',
                ],
            ],
            [
                '[isActive]' => '[active]',
                '[names]' => '[localizedNames]',
            ],
            [
                'isActive' => true,
                'active' => true,
                'names' => [
                    'en-US' => 'name',
                    'fr-FR' => 'nom',
                ],
                'localizedNames' => [
                    'en-US' => 'name',
                    'fr-FR' => 'nom',
                ],
            ],
        ];

        yield 'handle renaming of more advanced data structure' => [
            [
                'basicInformation' => [
                    'localizedNames' => [
                        'en-US' => 'name',
                        'fr-FR' => 'nom',
                    ],
                    'localizedDescriptions' => [
                        'en-US' => 'description',
                        'fr-FR' => 'description',
                    ],
                ],
            ],
            [
                '[basicInformation][localizedNames]' => '[names]',
                '[basicInformation][localizedDescriptions]' => '[descriptions]',
            ],
            [
                'basicInformation' => [
                    'localizedNames' => [
                        'en-US' => 'name',
                        'fr-FR' => 'nom',
                    ],
                    'localizedDescriptions' => [
                        'en-US' => 'description',
                        'fr-FR' => 'description',
                    ],
                ],
                'names' => [
                    'en-US' => 'name',
                    'fr-FR' => 'nom',
                ],
                'descriptions' => [
                    'en-US' => 'description',
                    'fr-FR' => 'description',
                ],
            ],
        ];

        yield 'handle property path that include an index placeholder' => [
            [
                'categoriesInformation' => [
                    'categoriesInformation' => [
                        [
                            'id' => 1,
                            'name' => 'Category 1',
                            'displayName' => 'Displayed category 1',
                        ],
                        [
                            'id' => 14,
                            'name' => 'Category 14',
                            'displayName' => 'Displayed category 14',
                        ],
                    ],
                ],
            ],
            [
                '[categoriesInformation][categoriesInformation][@index][id]' => '[categories][@index][categoryId]',
                '[categoriesInformation][categoriesInformation][@index][name]' => '[categories][@index][name]',
                '[categoriesInformation][categoriesInformation][@index][displayName]' => '[categories][@index][displayName]',
            ],
            [
                'categoriesInformation' => [
                    'categoriesInformation' => [
                        [
                            'id' => 1,
                            'name' => 'Category 1',
                            'displayName' => 'Displayed category 1',
                        ],
                        [
                            'id' => 14,
                            'name' => 'Category 14',
                            'displayName' => 'Displayed category 14',
                        ],
                    ],
                ],
                'categories' => [
                    [
                        'categoryId' => 1,
                        'name' => 'Category 1',
                        'displayName' => 'Displayed category 1',
                    ],
                    [
                        'categoryId' => 14,
                        'name' => 'Category 14',
                        'displayName' => 'Displayed category 14',
                    ],
                ],
            ],
        ];

        // Multiple index case is not handled yet, maybe an interesting improvement in the future if needed
        /*yield 'handle property path that include multiple index placeholder' => [
            [
                'attributeGroups' => [
                    'attributes' => [
                        [
                            'id' => 1,
                            'name' => 'Attribute 1',
                            'tags' => [
                                [
                                    'id' => 1,
                                    'value' => 'tag 1',
                                ],
                            ],
                        ],
                        [
                            'id' => 5,
                            'name' => 'Attribute 5',
                            'tags' => [
                                [
                                    'id' => 2,
                                    'value' => 'tag 2',
                                ],
                                [
                                    'id' => 8,
                                    'value' => 'tag 8',
                                ],
                                [
                                    'id' => 45,
                                    'value' => 'tag 45',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                '[attributeGroups][attributes][@attributeIndex][id]' => '[attributes][@attributeIndex][attributeId]',
                '[attributeGroups][attributes][@attributeIndex][name]' => '[attributes][@attributeIndex][name]',
                '[attributeGroups][attributes][@attributeIndex][tags][@tagIndex][id]' => '[attributes][@attributeIndex][tags][@tagIndex][tagId]',
                '[attributeGroups][attributes][@attributeIndex][tags][@tagIndex][value]' => '[attributes][@attributeIndex][tags][@tagIndex][value]',
            ],
            [
                'attributeGroups' => [
                    'attributes' => [
                        [
                            'id' => 1,
                            'name' => 'Attribute 1',
                            'tags' => [
                                [
                                    'id' => 1,
                                    'value' => 'tag 1',
                                ],
                            ],
                        ],
                        [
                            'id' => 5,
                            'name' => 'Attribute 5',
                            'tags' => [
                                [
                                    'id' => 2,
                                    'value' => 'tag 2',
                                ],
                                [
                                    'id' => 8,
                                    'value' => 'tag 8',
                                ],
                                [
                                    'id' => 45,
                                    'value' => 'tag 45',
                                ],
                            ],
                        ],
                    ],
                ],
                'attributes' => [
                    [
                        'attributeId' => 1,
                        'name' => 'Attribute 1',
                        'tags' => [
                            [
                                'tagId' => 1,
                                'value' => 'tag 1',
                            ],
                        ],
                    ],
                    [
                        'attributeId' => 5,
                        'name' => 'Attribute 5',
                        'tags' => [
                            [
                                'tagId' => 2,
                                'value' => 'tag 2',
                            ],
                            [
                                'tagId' => 8,
                                'value' => 'tag 8',
                            ],
                            [
                                'tagId' => 45,
                                'value' => 'tag 45',
                            ],
                        ],
                    ],
                ],
            ],
        ];*/
    }
}
