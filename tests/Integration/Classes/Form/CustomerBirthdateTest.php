<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Form;

use Configuration;
use Context;
use Customer;
use CustomerForm;
use CustomerFormatter;
use CustomerPersister;
use DateTime;
use Language;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The birthdate field was declared as a text input, so it was typed by hand in the shop's localised format
 * and the date branch both maintained themes already carry was never reached.
 */
class CustomerBirthdateTest extends KernelTestCase
{
    private CustomerForm $form;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;

        $context = Context::getContext();
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

        $translator = self::getContainer()->get('translator');
        $formatter = (new CustomerFormatter($translator, $context->language))
            ->setAskForBirthdate(true)
            ->setAskForPartnerOptin(false);

        $this->form = new CustomerForm(
            $context->smarty,
            $context,
            $translator,
            $formatter,
            new CustomerPersister($context, self::getContainer()->get('hashing'), $translator, false),
            []
        );
    }

    public function testTheFieldIsADateInput(): void
    {
        $this->assertSame('date', $this->form->getFormatter()->getFormat()['birthday']->getType());
    }

    /**
     * A date input reads its value as ISO, so a localised one leaves the field blank.
     */
    public function testTheStoredValueIsOfferedBackUnlocalised(): void
    {
        $customer = new Customer();
        $customer->birthday = '1990-05-17';

        $this->form->fillFromCustomer($customer);

        $this->assertSame('1990-05-17', $this->form->getField('birthday')->getValue());
    }

    /**
     * @dataProvider birthdayProvider
     */
    public function testWhatTheFormMakesOfASubmittedBirthdate(string $submitted, ?string $stored, bool $errors): void
    {
        $this->form->fillWith([
            'id_customer' => '',
            'firstname' => 'Birthdate',
            'lastname' => 'Probe',
            'email' => 'birthdate.probe.' . uniqid() . '@example.test',
            'password' => 'Correct horse battery',
            'birthday' => $submitted,
        ]);
        $this->form->validate();

        $field = $this->form->getField('birthday');
        $this->assertSame($errors, !empty($field->getErrors()));
        if (null !== $stored) {
            $this->assertSame($stored, $field->getValue());
        }
    }

    /**
     * @return array<string, array{0: string, 1: string|null, 2: bool}>
     */
    public function birthdayProvider(): array
    {
        return [
            // What a date input submits, whatever the shop's locale is, and already the stored form.
            'iso, what a date input submits' => ['1990-05-17', '1990-05-17', false],
            // What the field used to be filled with. Still accepted, and still converted.
            'the shop localised format' => [self::localised('1990-05-17'), '1990-05-17', false],
            // Neither: the form says so instead of leaving it to a message naming a property.
            'neither format' => ['hello', null, true],
        ];
    }

    private static function localised(string $iso): string
    {
        $language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

        return (new DateTime($iso))->format($language->date_format_lite);
    }
}
