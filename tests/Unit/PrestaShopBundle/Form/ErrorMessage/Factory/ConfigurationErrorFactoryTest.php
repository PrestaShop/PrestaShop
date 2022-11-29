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
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\AdministrationConfigurationError;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\CommonConfigurationError;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\ConfigurationErrorCollection;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\GeneralDataProvider;
use PrestaShopBundle\Form\ErrorMessage\Factory\AdministrationConfigurationErrorMessageProvider;
use PrestaShopBundle\Form\ErrorMessage\Factory\CommonConfigurationErrorMessageProvider;
use PrestaShopBundle\Form\ErrorMessage\Factory\ConfigurationErrorFactory;
use PrestaShopBundle\Form\ErrorMessage\LabelProvider;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\Translator;

class ConfigurationErrorFactoryTest extends TestCase
{
    public function testGetErrorMessages(): void
    {
        $translatorMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $translatorMock->method('trans')->willReturnMap(
            [
                [
                    '%s is invalid. Please enter an integer greater than or equal to 0.',
                    [
                        'Field',
                    ],
                    'Admin.Notifications.Error',
                    null,
                    'Field is invalid. Please enter an integer greater than or equal to 0.',
                ],
                [
                    '%s is invalid. Please enter an integer lower than %s.',
                    [
                        'Field',
                        GeneralDataProvider::MAX_COOKIE_VALUE,
                    ],
                    'Admin.Notifications.Error',
                    null,
                    'Field is invalid. Please enter an integer lower than 876000.',
                ],
                [
                    '%s is invalid.',
                    [
                        'Field',
                    ],
                    'Admin.Notifications.Error',
                    null,
                    'Field is invalid.',
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
        $labelProviderMock = $this
            ->getMockBuilder(LabelProvider::class)
            ->setMethods(['getLabel'])
            ->getMock();
        $labelProviderMock->method('getLabel')->willReturn('Field');
        $errorFactoryCollection = [];
        $errorFactoryCollection[] = new AdministrationConfigurationErrorMessageProvider($translatorMock);
        $errorFactoryCollection[] = new CommonConfigurationErrorMessageProvider($translatorMock, $languageRepositoryMock);
        $configurationErrorFactory = new ConfigurationErrorFactory($errorFactoryCollection, $labelProviderMock, $translatorMock);

        $formMock = $this->getMockBuilder(FormInterface::class)->getMock();

        $errorCollection = new ConfigurationErrorCollection();
        $errorCollection->add(new AdministrationConfigurationError(AdministrationConfigurationError::ERROR_COOKIE_LIFETIME_MAX_VALUE_EXCEEDED, 'field'));
        $errorCollection->add(new CommonConfigurationError(CommonConfigurationError::ERROR_NOT_NUMERIC_OR_LOWER_THAN_ZERO, 'field'));
        $errorCollection->add(new AdministrationConfigurationError(35, 'field'));

        $errorMessages = $configurationErrorFactory->getErrorMessages($errorCollection, $formMock);

        self::assertContains('Field is invalid. Please enter an integer lower than 876000.', $errorMessages);
        self::assertContains('Field is invalid. Please enter an integer greater than or equal to 0.', $errorMessages);
        self::assertContains('Field is invalid.', $errorMessages);
    }
}
