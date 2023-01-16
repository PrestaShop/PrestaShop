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

namespace Tests\Integration\PrestaShopBundle;

use PrestaShopBundle\Translation\PrestaShopTranslatorTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The purpose of this test is not to test that translation works for a specific language, but to check
 * that the different type of wordings (with parameters, plural, legacy placeholders, ...) can all be used
 * with the Symfony translator. It also validates that the modification from the PrestaShopTranslatorTrait
 * that handle the legacy use case don't break the native Symfony translator behaviour.
 */
class TranslationIntegrationTest extends KernelTestCase
{
    private $translator;

    public function setUp(): void
    {
        self::bootKernel();
        $this->translator = self::$kernel->getContainer()->get('translator');
    }

    /**
     * @dataProvider getExpectedTranslations
     *
     * @param string $expectedTranslatedMessage
     * @param string $message
     * @param string $domain
     * @param array $parameters
     * @param ?string $locale
     */
    public function testTranslator(string $expectedTranslatedMessage, string $message, string $domain, array $parameters, ?string $locale = null): void
    {
        self::assertEquals($expectedTranslatedMessage, $this->translator->trans($message, $parameters, $domain, $locale));
    }

    public function getExpectedTranslations(): iterable
    {
        yield 'simple translation' => [
            'A new order has been placed on your shop',
            'A new order has been placed on your shop',
            'Admin.Navigation.Header',
            [],
        ];
        yield 'simple translation with htmlspecialchars' => [
            'Succesful deletion',
            'Succesful deletion',
            'Admin.Notifications.Success',
            ['legacy' => 'htmlspecialchars'],
        ];
        yield 'translation with placeholder' => [
            'The root category of the shop myshop',
            'The root category of the shop %shop%',
            'Admin.Catalog.Notification',
            ['%shop%' => 'myshop'],
        ];
        yield 'translation with sprintf placeholders identified with index' => [
            'Image is too large (10 kB). Maximum allowed: 5 kB',
            'Image is too large (%1$d kB). Maximum allowed: %2$d kB',
            'Admin.Navigation.Search',
            [10, 5],
        ];
        yield 'translation with sprintf typed placeholders' => [
            '10 results match your query "stringTest".',
            '%d results match your query "%s".',
            'Admin.Navigation.Header',
            [10, 'stringTest'],
        ];
        yield 'translation with plural format' => [
            'This value is too short. It should have 3 characters or more.',
            'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.',
            'validators',
            // transChoice has been deprecated trans should be used instead with a %count% parameter
            ['{{ limit }}' => 3, '%count%' => 3],
        ];
        yield 'translation with singular format' => [
            'This value is too short. It should have 1 character or more.',
            'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.',
            'validators',
            ['{{ limit }}' => 1, '%count%' => 1],
        ];
        yield 'translation fallback to legacy system' => [
            'This is a bad idea',
            'This is a %message%',
            'Modules.Mymodule.Foobar',
            ['%message%' => 'bad idea'],
        ];
    }

    /**
     * @dataProvider getExpectedTransChoices
     *
     * @param string $expectedTranslatedMessage
     * @param string $message
     * @param string $domain
     * @param array $parameters
     */
    public function testTranslatorChoice(string $expectedTranslatedMessage, string $message, int $number, string $domain, array $parameters): void
    {
        self::assertEquals($expectedTranslatedMessage, $this->translator->transChoice($message, $number, $parameters, $domain));
    }

    public function getExpectedTransChoices(): iterable
    {
        yield 'translation with plural format' => [
            'This value is too short. It should have 3 characters or more.',
            'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.',
            3,
            'validators',
            ['{{ limit }}' => 3],
        ];
        yield 'translation with singular format' => [
            'This value is too short. It should have 1 character or more.',
            'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.',
            1,
            'validators',
            ['{{ limit }}' => 1],
        ];
    }
}
