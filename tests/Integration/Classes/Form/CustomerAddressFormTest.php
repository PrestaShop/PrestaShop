<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Form;

use Configuration;
use Country;
use CustomerAddressForm;
use CustomerAddressFormatter;
use CustomerAddressPersister;
use Language;
use PHPUnit\Framework\TestCase;
use Smarty;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerAddressFormTest extends TestCase
{
    /**
     * @var Country
     */
    private $country;

    /**
     * @var CustomerAddressForm
     */
    private $form;

    protected function setUp(): void
    {
        parent::setUp();

        $language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        // Poland: postcode format NN-NNN, postcode required
        $this->country = new Country((int) Country::getByIso('pl'), $language->id);
        $translator = $this->createMock(TranslatorInterface::class);

        $this->form = new CustomerAddressForm(
            $this->createMock(Smarty::class),
            $language,
            $translator,
            $this->createMock(CustomerAddressPersister::class),
            new CustomerAddressFormatter($this->country, $translator, [])
        );
    }

    /**
     * @dataProvider getPostcodesWithSpaces
     */
    public function testSpacesAroundThePostcodeAreRemoved(string $postcode): void
    {
        $this->form->fillWith(['id_country' => $this->country->id, 'postcode' => $postcode]);

        $this->assertSame('00-950', $this->form->getField('postcode')->getValue());
    }

    /**
     * @dataProvider getPostcodesWithSpaces
     */
    public function testPostcodeWithSpacesAroundItIsValid(string $postcode): void
    {
        $this->form->fillWith(['id_country' => $this->country->id, 'postcode' => $postcode]);
        $this->form->validate();

        $this->assertSame([], $this->form->getField('postcode')->getErrors());
    }

    public function testInvalidPostcodeIsStillRejected(): void
    {
        $this->form->fillWith(['id_country' => $this->country->id, 'postcode' => ' 00950 ']);
        $this->form->validate();

        $this->assertNotEmpty($this->form->getField('postcode')->getErrors());
    }

    public static function getPostcodesWithSpaces(): iterable
    {
        yield 'leading space' => [' 00-950'];
        yield 'trailing space' => ['00-950 '];
        yield 'both sides' => ['  00-950  '];
        yield 'tab and newline' => ["\t00-950\n"];
    }
}
