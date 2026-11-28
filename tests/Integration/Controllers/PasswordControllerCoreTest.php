<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Controllers;

use Context;
use Controller;
use Customer;
use PasswordControllerCore;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tools;

/**
 * The password recovery parameters come from the request and can be submitted as arrays
 * (e.g. email[]=...): they must be rejected with a validation error instead of a TypeError.
 */
class PasswordControllerCoreTest extends KernelTestCase
{
    private const CUSTOMER_EMAIL = 'password-controller-test@prestashop.com';
    private const CUSTOMER_PASSWORD = 'Old-Password-Not-Changed-2024!';
    private const NEW_PASSWORD = 'Correct-Horse-Battery-Staple-42!';

    private const INVALID_EMAIL_ERROR = 'Invalid email address.';
    private const INVALID_DATA_ERROR = 'We cannot regenerate your password with the data you\'ve submitted';
    private const EXPIRED_REQUEST_ERROR = 'The password change request expired. You should ask for a new one.';
    private const MISSING_PASSWORD_ERROR = 'The password is missing: please enter your new password.';
    private const EMPTY_CONFIRMATION_ERROR = 'The confirmation is empty: please fill in the password confirmation as well';

    /**
     * @var Customer|null
     */
    private $customer;

    /**
     * @var Controller|null
     */
    private $previousContextController;

    protected function setUp(): void
    {
        parent::setUp();

        // Building a controller registers it in the shared context, and the context controller
        // decides which container ObjectModel uses: it is restored after each test.
        $this->previousContextController = Context::getContext()->controller;
        $_GET = [];
        $_POST = [];
        Tools::resetRequest();
    }

    protected function tearDown(): void
    {
        Context::getContext()->controller = $this->previousContextController;
        if (null !== $this->customer) {
            $this->customer->delete();
            $this->customer = null;
        }
        $_GET = [];
        $_POST = [];
        Tools::resetRequest();

        parent::tearDown();
    }

    /**
     * @dataProvider invalidEmailProvider
     */
    public function testAnInvalidEmailIsRejected(array $get, array $post): void
    {
        $_GET = $get;
        $_POST = $post;

        $controller = $this->postProcess();

        self::assertSame([self::INVALID_EMAIL_ERROR], $controller->errors);
        self::assertSame('customer/password-email', $controller->renderedTemplate);
    }

    public static function invalidEmailProvider(): iterable
    {
        yield 'list in query' => [['email' => [self::CUSTOMER_EMAIL]], []];
        yield 'map in query' => [['email' => ['key' => self::CUSTOMER_EMAIL]], []];
        yield 'list in body' => [[], ['email' => [self::CUSTOMER_EMAIL]]];
        yield 'map in body' => [[], ['email' => ['key' => self::CUSTOMER_EMAIL]]];
        yield 'malformed string' => [[], ['email' => 'not-an-email']];
    }

    public function testANonStringTokenIsRejected(): void
    {
        $_GET = [
            'token' => ['key' => 'value'],
            'id_customer' => '1',
        ];

        $controller = $this->postProcess();

        self::assertSame([self::INVALID_DATA_ERROR], $controller->errors);
        self::assertSame('customer/password-email', $controller->renderedTemplate);
    }

    public function testANonStringResetTokenIsRejected(): void
    {
        $customer = $this->createCustomerWithResetToken();
        $_GET = [
            'token' => $customer->secure_key,
            'id_customer' => (string) $customer->id,
            'reset_token' => [$customer->reset_password_token],
        ];

        $controller = $this->postProcess();

        self::assertSame([self::EXPIRED_REQUEST_ERROR], $controller->errors);
        self::assertSame('customer/password-email', $controller->renderedTemplate);
    }

    /**
     * @dataProvider invalidPasswordProvider
     *
     * @param array|string $password
     * @param array|string $confirmation
     */
    public function testANonStringPasswordIsRejected($password, $confirmation, string $expectedError): void
    {
        $customer = $this->createCustomerWithResetToken();
        $_GET = [
            'token' => $customer->secure_key,
            'id_customer' => (string) $customer->id,
            'reset_token' => $customer->reset_password_token,
        ];
        $_POST = [
            'passwd' => $password,
            'confirmation' => $confirmation,
        ];

        $controller = $this->postProcess();

        self::assertContains($expectedError, $controller->errors);
        self::assertSame('customer/password-new', $controller->renderedTemplate);
        self::assertSame(
            $customer->passwd,
            (new Customer((int) $customer->id))->passwd,
            'The password must not be changed'
        );
    }

    public static function invalidPasswordProvider(): iterable
    {
        yield 'password as array' => [[self::NEW_PASSWORD], self::NEW_PASSWORD, self::MISSING_PASSWORD_ERROR];
        yield 'confirmation as array' => [self::NEW_PASSWORD, [self::NEW_PASSWORD], self::EMPTY_CONFIRMATION_ERROR];
        yield 'both as maps' => [['key' => self::NEW_PASSWORD], ['key' => self::NEW_PASSWORD], self::MISSING_PASSWORD_ERROR];
    }

    private function postProcess(): TestPasswordController
    {
        $controller = new TestPasswordController();
        $controller->postProcess();

        return $controller;
    }

    private function createCustomerWithResetToken(): Customer
    {
        $customer = new Customer();
        $customer->firstname = 'John';
        $customer->lastname = 'Doe';
        $customer->email = self::CUSTOMER_EMAIL;
        $customer->passwd = (new Hashing())->hash(self::CUSTOMER_PASSWORD);
        $customer->stampResetPasswordToken();
        $customer->add();

        return $this->customer = $customer;
    }
}

/**
 * Records the template instead of resolving it in the theme, so the test does not depend on the theme files.
 */
class TestPasswordController extends PasswordControllerCore
{
    /**
     * @var string|null
     */
    public $renderedTemplate;

    public function setTemplate($template, $params = [], $locale = null)
    {
        $this->renderedTemplate = $template;
    }
}
