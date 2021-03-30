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

namespace Tests\Unit\PrestaShopBundle\Form;

use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataError;
use Symfony\Component\Translation\Translator;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Form\InvalidConfigurationErrorMessageFactory;
use ReflectionClass;

class InvalidConfigurationErrorMessageFactoryTest extends TestCase
{
    public function testGetErrorMessageForConfigurationError()
    {
        $translatorMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->setMethods(['trans'])
            ->getMock();

        $translatorMock->method('trans')->willReturnMap(
            [
                [
                    'The "%s" field in %s is invalid. HTML tags are not allowed.',
                    [
                        'field',
                        'English',
                    ],
                    'Admin.Orderscustomers.Notification',
                    null,
                    'The "field" field in English is invalid. HTML tags are not allowed.'
                ],
                [
                    'The "%s" field is invalid. HTML tags are not allowed.',
                    [
                        'field',
                    ],
                    'Admin.Notifications.Error',
                    null,
                    'The "field" field is invalid. HTML tags are not allowed.'
                ],
            ]
        );

        $languageRepositoryMock = $this->getMockBuilder(LangRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneBy'])
            ->getMock();

        $language = new Lang();
        $language->setName('English');
        $languageRepositoryMock->method('findOneBy')->willReturn($language);
        $invalidConfigurationErrorMessageFactory = new InvalidConfigurationErrorMessageFactory($translatorMock, $languageRepositoryMock);
        $reflectionClass = new ReflectionClass(InvalidConfigurationErrorMessageFactory::class);
        $reflectionMethod = $reflectionClass->getMethod('getErrorMessageForConfigurationError');
        $reflectionMethod->setAccessible(true);

        $error = new InvalidConfigurationDataError(InvalidConfigurationDataError::ERROR_CONTAINS_HTML_TAGS, 'field', 1);
        $result = $reflectionMethod->invoke($invalidConfigurationErrorMessageFactory, $error, 'field');
        self::assertEquals(
            'The "field" field in English is invalid. HTML tags are not allowed.',
            $result
        );

        $error = new InvalidConfigurationDataError(InvalidConfigurationDataError::ERROR_CONTAINS_HTML_TAGS, 'field');
        $result = $reflectionMethod->invoke($invalidConfigurationErrorMessageFactory, $error, 'field');
        self::assertEquals(
            'The "field" field is invalid. HTML tags are not allowed.',
            $result
        );
    }
}
