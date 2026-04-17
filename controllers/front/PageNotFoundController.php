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
