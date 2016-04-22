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

class AddressControllerCore extends FrontController
{
    public $auth = false;
    public $guestAllowed = true;
    public $php_self = 'address';
    public $authRedirection = 'addresses';
    public $ssl = true;

    private $address_form;
    private $should_redirect = false;

    /**
     * Initialize address controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        $this->address_form = $this->makeAddressForm();
        $this->context->smarty->assign('address_form', $this->address_form->getProxy());
    }

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $this->context->smarty->assign('editing', false);
        $this->address_form->fillWith(Tools::getAllValues());
        if (Tools::isSubmit('submitAddress')) {
            if (!$this->address_form->submit()) {
                $this->errors[] = $this->l('Please fix the error below.');
            } else {
                if (Tools::getValue('id_address')) {
                    $this->success[] = $this->l('Address successfully updated!');
                } else {
                    $this->success[] = $this->l('Address successfully added!');
                }
                $this->should_redirect = true;
            }
        } elseif (($id_address = (int)Tools::getValue('id_address'))) {

            $addressForm = $this->address_form->loadAddressById($id_address);

            if ($addressForm->getAddress()->id === null) {
                return Tools::redirect('index.php?controller=404');
            }

            if (!$this->context->customer->isLogged()) {
                return $this->redirectWithNotifications('/index.php?controller=authentication');
            }

            if ($addressForm->getAddress()->id_customer != $this->context->customer->id) {
                return Tools::redirect('index.php?controller=404');
            }

            if (Tools::getValue('delete')) {
                $ok = $this->makeAddressPersister()->delete(
                    new Address($id_address, $this->context->language->id),
                    Tools::getValue('token')
                );
                if ($ok) {
                    $this->success[] = $this->l('Address successfully deleted!');
                    $this->should_redirect = true;
                } else {
                    $this->errors[] = $this->l('Could not delete address.');
                }
            } else {
                $this->context->smarty->assign('editing', true);
            }
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if ($this->ajax) {
            $this->ajaxDie(json_encode([
                'hasError'  => !empty($this->errors),
                'errors'    => $this->errors
            ]));
        } elseif ($this->should_redirect) {
            if (($back = Tools::getValue('back')) && Tools::secureReferrer($back)) {
                $mod = Tools::getValue('mod');
                $this->redirectWithNotifications('index.php?controller='.$back.($mod ? '&back='.$mod : ''));
            } else {
                $this->redirectWithNotifications('index.php?controller=addresses');
            }
        }

        parent::initContent();
        $this->setTemplate('customer/address.tpl');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Addresses', [], 'Breadcrumb'),
            'url' => $this->context->link->getPageLink('addresses')
        ];

        return $breadcrumb;
    }
}
