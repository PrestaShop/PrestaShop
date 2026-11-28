<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class Ps_TemplateOverrideSecond extends Module
{
    public const OUTPUT_CONFIG_KEY = 'TEMPLATE_OVERRIDE_SECOND_OUTPUT';

    public function __construct()
    {
        $this->name = 'ps_templateoverridesecond';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = 'Template override test module (second)';
        $this->description = 'Overrides front office templates and layouts for integration tests.';
    }

    public function install()
    {
        return parent::install() && $this->registerHook(['displayOverrideTemplate', 'overrideLayoutTemplate']);
    }

    public function hookDisplayOverrideTemplate(array $params)
    {
        return $this->getOutput();
    }

    public function hookOverrideLayoutTemplate(array $params)
    {
        return $this->getOutput();
    }

    private function getOutput(): ?string
    {
        $output = Configuration::get(self::OUTPUT_CONFIG_KEY);

        return false === $output ? null : $output;
    }
}
