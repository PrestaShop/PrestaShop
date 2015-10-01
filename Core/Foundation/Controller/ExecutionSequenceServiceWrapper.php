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
namespace PrestaShop\PrestaShop\Core\Foundation\Controller;

/**
 * This very simple wrapper implements all the interface methods,
 * but to return a void array of elements. This wrapper is usefull to have a Service
 * (that extends this) to implements only one or more of these methods.
 */
class ExecutionSequenceServiceWrapper implements ExecutionSequenceServiceInterface
{
    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ExecutionSequenceServiceInterface::getInitListeners()
     */
    public function getInitListeners()
    {
        return array();
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ExecutionSequenceServiceInterface::getBeforeListeners()
     */
    public function getBeforeListeners()
    {
        return array();
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ExecutionSequenceServiceInterface::getAfterListeners()
     */
    public function getAfterListeners()
    {
        return array();
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ExecutionSequenceServiceInterface::getCloseListeners()
     */
    public function getCloseListeners()
    {
        return array();
    }
}
