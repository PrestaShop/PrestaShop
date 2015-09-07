<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Foundation\Exception;

/**
 * This exception is thrown when a major error occurs, and we must stop the process.
 * Constructor will trigger an event in the 'message' EventDispatcher, with 'error_message' label.
 * But this exception is intented to be thrown when an evident error is caused by a wrong development
 * behavior: this should occurs only during the development process in case of a wrond API usage for example.
 *
 * Example of use: In an action when an important action failed, and you should avoid the process to continue.
 */
class DevelopmentErrorException extends ErrorException
{
}
