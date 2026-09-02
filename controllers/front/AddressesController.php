<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class AddressesControllerCore extends FrontController
{
    /** @var bool */
    public $auth = true;
    /** @var string */
    public $php_self = 'addresses';
    /** @var string */
    public $authRedirection = 'addresses';
    /** @var bool */
    public $ssl = true;

    /**
     * Initialize addresses controller.
     *
     * @see FrontController::init()
     */
    public function init(): void
    {
        parent::init();

        if (!Validate::isLoadedObject($this->context->customer)) {
            throw new PrestaShopException($this->trans('The customer could not be found.', [], 'Shop.Notifications.Error'));
        }
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        parent::initContent();
        $this->setTemplate('customer/addresses');
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Addresses', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('addresses'),
        ];

        return $breadcrumb;
    }
}
