<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Form\IdentifiableObject\Builder;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderFactory;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderFactoryInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;

class FormBuilderFactoryTest extends TestCase
{
    public function testCanBeConstructed()
    {
        $factory = new FormBuilderFactory(
            $this->createMock(FormFactoryInterface::class),
            $this->createMock(HookDispatcherInterface::class)
        );

        $this->assertInstanceOf(FormBuilderFactoryInterface::class, $factory);
    }

    public function testCreate()
    {
        // constructor mocks
        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $hookDispatcherMock = $this->createMock(HookDispatcherInterface::class);
        $dataProviderMock = $this->createMock(FormDataProviderInterface::class);

        $factory = new FormBuilderFactory(
            $formFactoryMock,
            $hookDispatcherMock
        );

        $builder = $factory->create('a', $dataProviderMock);

        $this->assertInstanceOf(FormBuilderInterface::class, $builder);
    }
}
