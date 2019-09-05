<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\TestCase;

use PHPUnit\Framework\BaseTestListener;
use PHPUnit_Framework_TestSuite;

class ErrorsDataListener extends BaseTestListener
{
    /**
     * @var PhpErrorsCounter a dedicated error handler
     */
    private $errorsCounter;

    /**
     * @var bool error handler is registered
     */
    private $isRegistered = false;

    /**
     * Internal tracking for test suites.
     *
     * Increments as more suites are run, then decremented as they finish. All
     * suites have been run when returns to 0.
     */
    protected $suites = 0;

    public function __construct()
    {
        $this->errorsCounter = new PhpErrorsCounter();
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        ++$this->suites;
        if (!$this->isRegistered) {
            $this->errorsCounter->registerErrorHandler();
            $this->isRegistered = true;
        }
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        --$this->suites;

        if ($this->suites === 0) {
            printf(PHP_EOL . PHP_EOL . 'Current report of phpErrorsHandler:');
            printf(PHP_EOL . $this->errorsCounter->displaySummary() . PHP_EOL);
            $this->errorsCounter->restoreErrorHandler();
        }
    }
}
