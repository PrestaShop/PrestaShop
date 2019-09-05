<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Util\String;

/**
 * This class defines reusable methods for checking strings under certain conditions.
 */
final class StringValidator implements StringValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function startsWith($string, $prefix)
    {
        return strpos($string, $prefix) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function endsWith($string, $suffix)
    {
        $length = strlen($suffix);

        if (0 === $length) {
            return false;
        }

        return substr($string, -$length) === $suffix;
    }

    /**
     * {@inheritdoc}
     */
    public function startsWithAndEndsWith($string, $prefix, $suffix)
    {
        return $this->startsWith($string, $prefix) && $this->endsWith($string, $suffix);
    }

    /**
     * {@inheritdoc}
     */
    public function doesContainsWhiteSpaces($string)
    {
        return preg_match('/\s/', $string);
    }
}
