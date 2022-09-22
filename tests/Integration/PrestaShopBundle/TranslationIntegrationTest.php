<?php

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
        yield 'translation with htmlspecialchars and sprintf' => [
            '&lt;a href="test"&gt;10 Succesful deletion "&lt;b&gt;stringTest&lt;/b&gt;"&lt;/a&gt;',
            '<a href="test">%d Succesful deletion "%s"</a>',
            'Admin.Notifications.Success',
            ['legacy' => 'htmlspecialchars', 10, '<b>stringTest</b>'],
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
