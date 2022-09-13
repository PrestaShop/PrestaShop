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

namespace Tests\TestCase;

class PhpErrorsCounter
{
    private $notices = 0;
    private $warnings = 0;
    private $errors = 0;
    private $deprecations = 0;

    /**
     * This error handler allow us to count every errors
     * in our test suite. Once we will have fixed all we will
     * enable the error handler of PHPUnit that convert errors to exceptions.
     */
    public function registerErrorHandler()
    {
        set_error_handler([$this, 'errorHandler'], E_ALL);
    }

    public function errorHandler(int $errorType): bool
    {
        switch ($errorType) {
            case E_WARNING:
                $this->warnings++;
                break;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $this->deprecations++;
                break;
            case E_ERROR:
                $this->errors++;
                break;
            case E_NOTICE:
                $this->notices++;
                break;
            default:
                // nothing to do.
        }

        return false;
    }

    public function restoreErrorHandler()
    {
        restore_error_handler();
    }

    /**
     * @return int the number of notices
     */
    public function getNotices()
    {
        return $this->notices;
    }

    /**
     * @return int the number of warnings
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @return int the number of deprecations
     */
    public function getDeprecations()
    {
        return $this->deprecations;
    }

    /**
     * @return int the number of errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return string a summary report of errors
     */
    public function displaySummary()
    {
        return sprintf(
            'Errors: %d / Warnings: %d / Notices: %d / Deprecations: %d',
            $this->getErrors(),
            $this->getWarnings(),
            $this->getNotices(),
            $this->getDeprecations()
        );
    }

    /**
     * Reset all counters to 0.
     */
    public function reset()
    {
        $this->deprecations = 0;
        $this->errors = 0;
        $this->notices = 0;
        $this->warnings = 0;
    }
}
