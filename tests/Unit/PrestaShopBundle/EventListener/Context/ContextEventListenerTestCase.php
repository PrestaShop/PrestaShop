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

namespace Tests\Unit\PrestaShopBundle\EventListener\Context;

use Context;
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Tests\Resources\classes\TestCookie;

abstract class ContextEventListenerTestCase extends KernelTestCase
{
    /**
     * Since the listeners job is mostly to fill the builders's properties, that are private, this accessor method is convenient
     *
     * @param $object
     * @param string $propertyName
     *
     * @return mixed
     */
    protected function getPrivateField($object, string $propertyName)
    {
        $reflectionProperty = new ReflectionProperty(get_class($object), $propertyName);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }

    protected function mockLegacyContext(array $cookieValues): LegacyContext|MockObject
    {
        $cookie = new TestCookie();
        foreach ($cookieValues as $cookieKey => $cookieValue) {
            $cookie->{$cookieKey} = $cookieValue;
        }

        $context = $this->createMock(Context::class);
        $context->cookie = $cookie;

        $legacyContext = $this->createMock(LegacyContext::class);
        $legacyContext
            ->method('getContext')
            ->willReturn($context)
        ;

        return $legacyContext;
    }

    protected function mockConfiguration(array $configurationValues): ShopConfigurationInterface|MockObject
    {
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration
            ->method('get')
            ->will($this->returnCallback(function ($configurationName) use ($configurationValues) {
                if (isset($configurationValues[$configurationName])) {
                    return $configurationValues[$configurationName];
                }

                throw new \InvalidArgumentException('Unhandled configuration ' . $configurationName);
            }))
        ;

        return $configuration;
    }

    protected function createRequestEvent(Request $request): RequestEvent
    {
        return new RequestEvent(static::createKernel(), $request, HttpKernelInterface::MAIN_REQUEST);
    }
}
