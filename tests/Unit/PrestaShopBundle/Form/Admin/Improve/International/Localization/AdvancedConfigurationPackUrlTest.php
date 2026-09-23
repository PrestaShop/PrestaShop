<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Improve\International\Localization;

use Language;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Localization\AdvancedConfiguration;
use PrestaShopBundle\Form\Admin\Improve\International\Localization\AdvancedConfigurationType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The pack URLs carry %version% and %locale% placeholders, so the URL constraint has to accept a
 * percent sign, and a URL without %locale% has to be refused: it would download one language for
 * every language installed and the failure would be silent, since the response is a valid archive.
 */
class AdvancedConfigurationPackUrlTest extends TypeTestCase
{
    private const VALID = 'https://i18n.prestashop-project.org/translations/%version%/%locale%/%locale%.zip';

    public function testAPackUrlKeepsItsPlaceholders(): void
    {
        $form = $this->submit(self::VALID);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid(), $this->errorsOf($form));
        $this->assertSame(self::VALID, $form->get('language_pack_url')->getData());
    }

    public function testAUrlWithoutTheLocalePlaceholderIsRefused(): void
    {
        $form = $this->submit('https://example.test/translations/9.2.0/pack.zip');

        $this->assertFalse($form->isValid());
    }

    public function testSomethingThatIsNotAUrlIsRefused(): void
    {
        $form = $this->submit('%locale% is not a url');

        $this->assertFalse($form->isValid());
    }

    public function testAnEmptyPackUrlIsRefused(): void
    {
        $form = $this->submit('');

        $this->assertFalse($form->isValid());
    }

    /**
     * A shop upgraded from before these settings existed has no configuration row for them. The form
     * requires a value, so the read side has to answer with the constants or the whole Advanced page
     * becomes unsavable until the merchant retypes both URLs.
     */
    public function testAShopWithNoStoredValueFallsBackToTheShippedDefaults(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('get')->willReturn(false);

        $read = (new AdvancedConfiguration($configuration))->getConfiguration();

        $this->assertSame(Language::SF_LANGUAGE_PACK_URL, $read['language_pack_url']);
        $this->assertSame(Language::EMAILS_LANGUAGE_PACK_URL, $read['emails_pack_url']);
    }

    private function submit(string $languagePackUrl): FormInterface
    {
        $form = $this->factory->create(AdvancedConfigurationType::class);
        $form->submit([
            'language_identifier' => 'en',
            'country_identifier' => 'gb',
            'language_pack_url' => $languagePackUrl,
            'emails_pack_url' => self::VALID,
        ]);

        return $form;
    }

    private function errorsOf(FormInterface $form): string
    {
        $messages = [];

        foreach ($form->getErrors(true) as $error) {
            $messages[] = $error->getMessage();
        }

        return implode(' | ', $messages);
    }

    protected function getExtensions(): array
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        // A real validator, not the mock ValidatorExtensionTrait provides: that one returns an empty
        // violation list whatever is submitted, so every rejection case below would pass vacuously.
        return [
            new PreloadedExtension([new AdvancedConfigurationType($translator, [])], []),
            new ValidatorExtension(Validation::createValidator()),
        ];
    }
}
