<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Form\IdentifiableObject\Handler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerFactory;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerFactoryInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FormHandlerFactoryTest extends TestCase
{
    public function testCanBeConstructed()
    {
        $factory = new FormHandlerFactory(
            $this->createMock(HookDispatcherInterface::class),
            $this->createMock(TranslatorInterface::class),
            true
        );

        $this->assertInstanceOf(FormHandlerFactoryInterface::class, $factory);

        return $factory;
    }

    /**
     * @depends testCanBeConstructed
     */
    public function testItCreatesFormHandler(FormHandlerFactoryInterface $factory)
    {
        $formHandler = $factory->create(
            $this->createMock(FormDataHandlerInterface::class)
        );

        $this->assertInstanceOf(FormHandlerInterface::class, $formHandler);
    }
}
