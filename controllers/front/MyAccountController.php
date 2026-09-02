<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class MyAccountControllerCore extends FrontController
{
    /** @var bool */
    public $auth = true;
    /** @var string */
    public $php_self = 'my-account';
    /** @var string */
    public $authRedirection = 'my-account';
    /** @var bool */
    public $ssl = true;

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        parent::initContent();
        $this->setTemplate('customer/my-account');
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }
}
