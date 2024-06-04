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

namespace Core\Form\IdentifiableObject\DataProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\EditableCarrier;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\CarrierFormDataProvider;

class CarrierFormDataProviderTest extends TestCase
{
    public function testGetData(): void
    {
        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus
            ->method('handle')
            ->with($this->isInstanceOf(GetCarrierForEditing::class))
            ->willReturn(new EditableCarrier(
                42,
                'Carrier name',
                5,
                'http://track.to',
                1,
                true,
                [
                    1 => 'English delay',
                    2 => 'French delay',
                ],
                '/img/c/45.jkg',
            ))
        ;

        $formDataProvider = new CarrierFormDataProvider($queryBus);
        $formData = $formDataProvider->getData(42);
        $this->assertEquals([
            'general_settings' => [
                'name' => 'Carrier name',
                'localized_delay' => [
                    1 => 'English delay',
                    2 => 'French delay',
                ],
                'active' => true,
                'grade' => 5,
                'logo_preview' => '/img/c/45.jkg',
                'tracking_url' => 'http://track.to',
            ],
        ], $formData);
    }

    public function testGetDefaultData(): void
    {
        $formDataProvider = new CarrierFormDataProvider($this->createMock(CommandBusInterface::class));
        $this->assertEquals([
            'general_settings' => [
                'grade' => 0,
            ],
        ], $formDataProvider->getDefaultData());
    }
}
