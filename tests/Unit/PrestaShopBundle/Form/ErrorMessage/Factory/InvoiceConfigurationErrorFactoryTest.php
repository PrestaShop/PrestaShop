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
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\InvoiceConfigurationError;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Form\ErrorMessage\Factory\InvoiceConfigurationErrorFactory;
use Symfony\Component\Translation\Translator;

class InvoiceConfigurationErrorFactoryTest extends TestCase
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
                    'Invalid "%s" date.',
                    [
                        'From',
                    ],
                    'Admin.Orderscustomers.Notification',
                    null,
                    'Invalid "From" date.',
                ],
                [
                    'Invalid "%s" date.',
                    [
                        'To',
                    ],
                    'Admin.Orderscustomers.Notification',
                    null,
                    'Invalid "To" date.',
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
        $invoiceConfigurationErrorFactory = new InvoiceConfigurationErrorFactory($translatorMock, $languageRepositoryMock);

        $error = new InvoiceConfigurationError(InvoiceConfigurationError::ERROR_INVALID_DATE_TO, 'to', 1);
        $result = $invoiceConfigurationErrorFactory->getErrorMessageForConfigurationError($error, 'To');
        self::assertEquals(
            'Invalid "From" date.',
            $result
        );

        $error = new InvoiceConfigurationError(InvoiceConfigurationError::ERROR_INVALID_DATE_FROM, 'from');
        $result = $invoiceConfigurationErrorFactory->getErrorMessageForConfigurationError($error, 'From');

        self::assertEquals(
            'Invalid "To" date.',
            $result
        );

        $error = new InvoiceConfigurationError('non_existing_error_Code', 'field');
        $result = $invoiceConfigurationErrorFactory->getErrorMessageForConfigurationError($error, 'field');

        self::assertNull($result);
    }
}
