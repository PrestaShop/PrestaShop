<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class Ps_MailExtraVarsTest extends Module
{
    public const PLACEHOLDER = '{ps_mailextravarstest_placeholder}';
    public const VALUE = 'value injected by a module';

    public function __construct()
    {
        $this->name = 'ps_mailextravarstest';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = 'Mail extra template vars test module';
        $this->description = 'Adds one extra mail template variable for integration tests.';
    }

    public function install()
    {
        return parent::install() && $this->registerHook('actionGetExtraMailTemplateVars');
    }

    public function hookActionGetExtraMailTemplateVars(array $params)
    {
        $params['extra_template_vars'][self::PLACEHOLDER] = self::VALUE;
    }
}
