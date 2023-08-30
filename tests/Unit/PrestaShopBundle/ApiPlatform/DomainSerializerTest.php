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

namespace Tests\Unit\PrestaShopBundle\ApiPlatform;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShopBundle\ApiPlatform\DomainSerializer;
use PrestaShopBundle\ApiPlatform\Normalizer\DecimalNumberDenormalizer;
use PrestaShopBundle\ApiPlatform\Normalizer\ObjectDenormalizer;

class DomainSerializerTest extends TestCase
{
    /**
     * @var DomainSerializer
     */
    private DomainSerializer $serializer;

    /**
     * Set up dependencies for HookStatusProvider
     */
    public function setUp(): void
    {
        $denormalizers = new \ArrayIterator([new DecimalNumberDenormalizer(), new ObjectDenormalizer()]);
        $this->serializer = new DomainSerializer($denormalizers);
    }

    /**
     * @dataProvider getExpectedDenormalizedData
     */
    public function testDenormalize(array $arrayToDenormalize, $denormalizedObject): void
    {
        self::assertEquals($denormalizedObject, $this->serializer->denormalize($arrayToDenormalize, get_class($denormalizedObject)));
    }

    public function getExpectedDenormalizedData()
    {
        yield [
            [
                'localizedNames' => [
                    1 => 'test1',
                    2 => 'test2',
                ],
                'reductionPercent' => 10.3,
                'displayPriceTaxExcluded' => true,
                'showPrice' => true,
                'shopIds' => [1],
            ],
            new AddCustomerGroupCommand(
                [
                    1 => 'test1',
                    2 => 'test2',
                ],
                new DecimalNumber('10.3'),
                true,
                true,
                [1]
            ),
        ];
    }
}
