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

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/php_errors.html.twig')]
class PHPErrors
{
    private ?array $phpErrors = null;

    public function getPhpErrors(): array
    {

        if ($this->phpErrors === null) {
            $this->phpErrors = [];
            if (_PS_MODE_DEV_) {
                set_error_handler(function ($errno, $errstr, $errfile, $errline): bool
                {
                    /**
                     * Prior to PHP 8.0.0, the $errno value was always 0 if the expression which caused the diagnostic was prepended by the @ error-control operator.
                     *
                     * @see https://www.php.net/manual/fr/function.set-error-handler.php
                     * @see https://www.php.net/manual/en/language.operators.errorcontrol.php
                     */
                    if (!(error_reporting() & $errno)) {
                        return false;
                    }

                    switch ($errno) {
                        case E_USER_ERROR:
                        case E_ERROR:
                            die('Fatal error: ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
                        case E_USER_WARNING:
                        case E_WARNING:
                            $type = 'Warning';

                            break;
                        case E_USER_NOTICE:
                        case E_NOTICE:
                            $type = 'Notice';

                            break;
                        default:
                            $type = 'Unknown error';

                            break;
                    }

                    $this->phpErrors = [
                        'type' => $type,
                        'errline' => (int) $errline,
                        'errfile' => str_replace('\\', '\\\\', $errfile), // Hack for Windows paths
                        'errno' => (int) $errno,
                        'errstr' => $errstr,
                    ];

                    return true;
                });
            }
        }

        return $this->phpErrors;
    }
}
