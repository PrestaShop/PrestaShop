<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class PageNotFoundControllerCore extends FrontController
{
    /** @var string */
    public $php_self = 'pagenotfound';
    /** @var string */
    public $page_name = 'pagenotfound';
    /** @var bool */
    public $ssl = true;

    /**
     * @see FrontController::init()
     */
    public function init()
    {
        Hook::exec('actionNotFound');
        parent::init();
    }

    /**
     * Without an entry of its own the breadcrumb holds nothing but the home link, and a
     * one-level breadcrumb is what themes hide as empty. The wording matches the meta title
     * this page is registered with.
     */
    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('404 error', [], 'Shop.Navigation'),
            'url' => $this->context->link->getPageLink('pagenotfound'),
        ];

        return $breadcrumb;
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        $this->context->cookie->disallowWriting();
        parent::initContent();
        $this->setTemplate('errors/404');
    }

    protected function canonicalRedirection(string $canonical_url = ''): void
    {
        // 404 - no need to redirect to the canonical url
    }

    protected function sslRedirection(): void
    {
        // 404 - no need to redirect
    }

    /**
     * Initializes a set of commonly used variables related to the current page, available for use
     * in the template. @see FrontController::assignGeneralPurposeVariables for more information.
     *
     * @return array
     */
    public function getTemplateVarPage(): array
    {
        $page = parent::getTemplateVarPage();
        $page['title'] = $this->trans('The page you are looking for was not found.', [], 'Shop.Theme.Global');

        return $page;
    }

    public function displayAjax(): void
    {
        header('Content-Type: application/json');
        echo json_encode($this->trans('The page you are looking for was not found.', [], 'Shop.Theme.Global'));
    }
}
