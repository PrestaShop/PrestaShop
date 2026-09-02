<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class IndexControllerCore extends FrontController
{
    /** @var string */
    public $php_self = 'index';

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        parent::initContent();
        $this->context->smarty->assign([
            'HOOK_HOME' => Hook::exec('displayHome'),
        ]);
        $this->setTemplate('index');
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalURL(): string
    {
        return $this->context->link->getPageLink('index');
    }
}
