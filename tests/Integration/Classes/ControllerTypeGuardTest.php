<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Context;
use Manufacturer;
use PHPUnit\Framework\TestCase;
use Supplier;

/**
 * The context has no controller when the code runs outside a request, which is the case for the Admin
 * API, a console command or a script. Listing methods read the controller's type to decide whether they
 * are serving the front office, and reading it off a null controller raises "Attempt to read property
 * controller_type on null" on every call.
 */
class ControllerTypeGuardTest extends TestCase
{
    /**
     * @var mixed
     */
    private $previousController;

    /**
     * @var array<int, string>
     */
    private array $warnings = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousController = Context::getContext()->controller;
        Context::getContext()->controller = null;

        $this->warnings = [];
        set_error_handler(function (int $errno, string $message): bool {
            $this->warnings[] = $message;

            return true;
        }, E_WARNING);
    }

    protected function tearDown(): void
    {
        restore_error_handler();
        Context::getContext()->controller = $this->previousController;

        parent::tearDown();
    }

    public function testManufacturerListsProductsWithoutAControllerInContext(): void
    {
        $products = (new Manufacturer(1))->getProductsLite((int) Configuration::get('PS_LANG_DEFAULT'));

        $this->assertSame([], $this->warnings);
        $this->assertIsIterable($products);
    }

    public function testSupplierListsProductsWithoutAControllerInContext(): void
    {
        $products = Supplier::getProducts(1, (int) Configuration::get('PS_LANG_DEFAULT'), 1, 10);

        $this->assertSame([], $this->warnings);
        $this->assertIsIterable($products);
    }
}
