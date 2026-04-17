<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class AddressControllerCore extends FrontController
{
    /** @var bool */
    public $auth = true;
    /** @var bool */
    public $guestAllowed = true;
    /** @var string */
    public $php_self = 'address';
    /** @var string */
    public $authRedirection = 'addresses';
    /** @var bool */
    public $ssl = true;

    protected $address_form;
    protected $should_redirect = false;

    /**
     * Initialize address controller.
     *
     * @see FrontController::init()
     */
    public function init(): void
    {
        parent::init();
        $this->address_form = $this->makeAddressForm();
        $this->context->smarty->assign('address_form', $this->address_form->getProxy());
    }

    /**
     * Start forms process.
     *
     * @see FrontController::postProcess()
     */
    public function postProcess(): void
    {
        $this->context->smarty->assign('editing', false);
        $id_address = (int) Tools::getValue('id_address');
        // Initialize address if an id exists
        if ($id_address) {
            $this->address_form->loadAddressById($id_address);
        }

        // Fill the form with data
        $this->address_form->fillWith(Tools::getAllValues());

        // Submit the address, don't care if it's an edit or add
        if (Tools::isSubmit('submitAddress')) {
            if (!$this->address_form->submit()) {
                $this->errors[] = $this->trans('Please fix the error below.', [], 'Shop.Notifications.Error');
            } else {
                if ($id_address) {
                    $this->success[] = $this->trans('Address successfully updated.', [], 'Shop.Notifications.Success');
                } else {
                    $this->success[] = $this->trans('Address successfully added.', [], 'Shop.Notifications.Success');
                }

                $this->should_redirect = true;
            }
        }

        // There is no id_adress, no need to continue
        if (!$id_address) {
            return;
        }

        if (Tools::getValue('delete')) {
            if (
                Validate::isLoadedObject($this->context->cart)
                && ($this->context->cart->id_address_invoice == $id_address
                || $this->context->cart->id_address_delivery == $id_address)
            ) {
                $this->errors[] = $this->trans(
                    'Could not delete the address since it is used in the shopping cart.',
                    [],
                    'Shop.Notifications.Error'
                );

                return;
            }

            $ok = $this->makeAddressPersister()->delete(
                new Address($id_address, $this->context->language->id),
                Tools::getValue('token')
            );
            if ($ok) {
                $this->success[] = $this->trans('Address successfully deleted.', [], 'Shop.Notifications.Success');
                $this->should_redirect = true;
            } else {
                $this->errors[] = $this->trans('Could not delete address.', [], 'Shop.Notifications.Error');
            }
        } else {
            $this->context->smarty->assign('editing', true);
        }
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        if (!$this->ajax && $this->should_redirect) {
            if (($back = Tools::getValue('back')) && Tools::urlBelongsToShop($back)) {
                $mod = Tools::getValue('mod');
                $this->redirectWithNotifications('index.php?controller=' . $back . ($mod ? '&back=' . $mod : ''));
            } else {
                $this->redirectWithNotifications($this->context->link->getPageLink('addresses'));
            }
        }

        parent::initContent();
        $this->setTemplate(
            'customer/address',
            [
                'entity' => 'address',
                'id' => (int) Tools::getValue('id_address'),
            ]
        );
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Addresses', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('addresses'),
        ];

        $id_address = Tools::getValue('id_address');
        $title = $id_address
            ? $this->trans('Update your address', [], 'Shop.Theme.Customeraccount')
            : $this->trans('New address', [], 'Shop.Theme.Customeraccount');

        $breadcrumb['links'][] = [
            'title' => $title,
            'url' => '#',
        ];

        return $breadcrumb;
    }

    public function displayAjaxAddressForm(): void
    {
        $addressForm = $this->makeAddressForm();

        if (Tools::getIsset('id_address') && ($id_address = (int) Tools::getValue('id_address'))) {
            $addressForm->loadAddressById($id_address);
        }

        if (Tools::getIsset('id_country')) {
            $addressForm->fillWith(['id_country' => Tools::getValue('id_country')]);
        }

        ob_end_clean();
        header('Content-Type: application/json');
        $this->ajaxRender(json_encode([
            'address_form' => $this->render(
                'customer/_partials/address-form',
                $addressForm->getTemplateVariables()
            ),
        ]));
    }
}
