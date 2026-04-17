<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Utility;

use Context;
use PaymentModule;

/**
 * fake class for payment module
 * could be used as anonymous class with php > 7
 * $paymentModule = new class extends PaymentModule
 * {
 * public $active = 1;
 * public $name = 'payment_test';
 * public $displayName = 'Test order';
 * };
 */
class PaymentModuleFake extends PaymentModule
{
    public function __construct($name = null, ?Context $context = null)
    {
        parent::__construct($name, $context);

        $this->active = true;
        $this->name = 'payment_test';
        $this->displayName = 'Test order';
    }
}
