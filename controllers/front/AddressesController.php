<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class AddressesControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'addresses';
    public $authRedirection = 'addresses';
    public $ssl = true;

    /**
     * Initialize addresses controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        if (!Validate::isLoadedObject($this->context->customer)) {
            die($this->trans('The customer could not be found.', array(), 'Shop.Notifications.Error'));
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if (count($this->context->customer->getSimpleAddresses()) <= 0) {
            $link = '<a href="'.$this->context->link->getPageLink('address', true).'">'.$this->trans('Add a new address', array(), 'Shop.Theme.Actions').'</a>';
            $this->warning[] = $this->trans('No addresses are available. %s', array($link), 'Shop.Notifications.Success');
        }

        parent::initContent();
        $this->setTemplate('customer/addresses');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }
}
