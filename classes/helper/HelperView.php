<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class HelperViewCore extends Helper
{
    public $id;
    public $toolbar = true;
    public $table;
    public $token;

    /** @var string|null If not null, a title will be added on that list */
    public $title = null;

    public function __construct()
    {
        $this->base_folder = 'helpers/view/';
        $this->base_tpl = 'view.tpl';
        parent::__construct();
    }

    public function generateView()
    {
        $this->tpl = $this->createTemplate($this->base_tpl);

        $this->tpl->assign([
            'title' => $this->title,
            'current' => $this->currentIndex,
            'token' => $this->token,
            'table' => $this->table,
            'show_toolbar' => $this->show_toolbar,
            'toolbar_scroll' => $this->toolbar_scroll,
            'toolbar_btn' => $this->toolbar_btn,
            'link' => $this->context->link,
            'controller_name' => $this->controller_name,
        ]);

        return parent::generate();
    }
}
