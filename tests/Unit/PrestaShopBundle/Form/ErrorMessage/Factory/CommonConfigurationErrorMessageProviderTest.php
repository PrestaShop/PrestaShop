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

namespace Tests\Unit\PrestaShopBundle\Form\ErrorMessage\Factory;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\CommonConfigurationError;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\ConfigurationErrorInterface;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\InvoiceConfigurationError;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Form\ErrorMessage\Factory\CommonConfigurationErrorMessageProvider;
use Symfony\Component\Translation\Translator;

class CommonConfigurationErrorMessageProviderTest extends TestCase
{
    public function testGetErrorMessageForConfigurationError(): void
    {
        $translatorMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->setMethods(['trans'])
            ->getMock();

        $translatorMock->method('trans')->willReturnMap(
            [
                [
                    '%s is invalid. Please enter an integer greater than or equal to 0.',
                    [
                        'field',
                    ],
                    'Admin.Notifications.Error',
                    null,
                    'Field is invalid. Please enter an integer greater than or equal to 0.',
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
        $commonConfigurationErrorFactory = new CommonConfigurationErrorMessageProvider($translatorMock, $languageRepositoryMock);

        $error = new CommonConfigurationError(CommonConfigurationError::ERROR_NOT_NUMERIC_OR_LOWER_THAN_ZERO, 'field');
        $result = $commonConfigurationErrorFactory->getErrorMessageForConfigurationError($error, 'field');
        self::assertEquals(
            'Field is invalid. Please enter an integer greater than or equal to 0.',
            $result
        );

        $error = new CommonConfigurationError(35, 'field');
        $result = $commonConfigurationErrorFactory->getErrorMessageForConfigurationError($error, 'field');

        self::assertNull($result);
    }
}
