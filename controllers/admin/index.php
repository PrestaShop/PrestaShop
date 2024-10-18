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

class MyController
{
    public function myMethod()
    {
        // Set caching headers
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');

        // Start the session
        session_start();

        // Check if the user is logged in
        if (!isset($_SESSION['user_id'])) {
            // Log the attempt (you could write to a file or a logging system)
            error_log("Unauthorized access attempt on " . $_SERVER['REQUEST_URI']);

            // Redirect to login page with an error message
            $_SESSION['error_message'] = 'You must be logged in to access this page.';
            header('Location: ../../login.php');
            exit;
        }

        // Perform your logic here (e.g., processing data, etc.)
        // Example: Check user permissions, retrieve data, etc.

        // If everything is fine, redirect to a specific admin page
        header('Location: ../../admin/dashboard.php');
        exit; // Ensure no further code is executed
    }
}

