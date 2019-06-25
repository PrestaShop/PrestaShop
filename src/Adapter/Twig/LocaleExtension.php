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

namespace PrestaShop\PrestaShop\Adapter\Twig;

use DateTime;
use Language;
use Twig\Extension\AbstractExtension;
use Twig_SimpleFunction;

/**
 * Provides helper functions in Twig for formatting data using context locale
 */
final class LocaleExtension extends AbstractExtension
{
    /**
     * @var Language
     */
    private $contextLanguage;

    /**
     * @param Language $contextLanguage
     */
    public function __construct(Language $contextLanguage)
    {
        $this->contextLanguage = $contextLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'format_date',
                function ($date) {
                    return (new DateTime($date))->format($this->contextLanguage->date_format_lite);
                }
            ),
        ];
    }
}
