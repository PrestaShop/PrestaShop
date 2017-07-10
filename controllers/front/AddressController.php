<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
                $this->errors[] = $this->trans('Please fix the error below.', array(), 'Shop.Notifications.Error');
            } else {
                if (Tools::getValue('id_address')) {
                    $this->success[] = $this->trans('Address successfully updated!', array(), 'Shop.Notifications.Success');
                } else {
                    $this->success[] = $this->trans('Address successfully added!', array(), 'Shop.Notifications.Success');
                }
                $this->should_redirect = true;
            }
        } elseif (($id_address = (int)Tools::getValue('id_address'))) {
            $this->address_form->loadAddressById($id_address);

            if (Tools::getValue('delete')) {
                $ok = $this->makeAddressPersister()->delete(
                    new Address($id_address, $this->context->language->id),
                    Tools::getValue('token')
                );
                if ($ok) {
                    $this->success[] = $this->trans('Address successfully deleted!', array(), 'Shop.Notifications.Success');
                    $this->should_redirect = true;
                } else {
                    $this->errors[] = $this->trans('Could not delete address.', array(), 'Shop.Notifications.Error');
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
        if (!$this->ajax && $this->should_redirect) {
            if (($back = Tools::getValue('back')) && Tools::secureReferrer($back)) {
                $mod = Tools::getValue('mod');
                $this->redirectWithNotifications('index.php?controller='.$back.($mod ? '&back='.$mod : ''));
            } else {
                $this->redirectWithNotifications('index.php?controller=addresses');
            }
        }

        parent::initContent();
        $this->setTemplate('customer/address', array('entity' => 'address', 'id' => Tools::getValue('id_address')));
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Addresses', array(), 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('addresses')
        ];

        return $breadcrumb;
    }

    public function displayAjaxAddressForm()
    {
        $addressForm = $this->makeAddressForm();

        if (Tools::getIsset('id_address') && ($id_address = (int)Tools::getValue('id_address'))) {
            $addressForm->loadAddressById($id_address);
        }

        if (Tools::getIsset('id_country')) {
            $addressForm->fillWith(array('id_country' => Tools::getValue('id_country')));
        }

        ob_end_clean();
        header('Content-Type: application/json');
        $this->ajaxDie(Tools::jsonEncode(array(
            'address_form' => $this->render(
                'customer/_partials/address-form',
                $addressForm->getTemplateVariables()
            ),
        )));
    }
}
