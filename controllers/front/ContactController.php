<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class ContactControllerCore extends FrontController
{
    /** @var string */
    public $php_self = 'contact';
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

        $this->setTemplate('contact');
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Contact us', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('contact'),
        ];

        return $breadcrumb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalURL(): string
    {
        return $this->context->link->getPageLink('contact');
    }
}
