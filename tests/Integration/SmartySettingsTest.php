<?php

namespace PrestaShop\PrestaShop\Tests\Integration;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;

class SmartySettingsTest extends IntegrationTestCase
{
    private $smarty;

    public function setup()
    {
        global $smarty;
        $this->smarty = $smarty;
        $this->smarty->force_compile = true;
    }

    private function render($templateString, array $parameters)
    {
        $this->smarty->assign($parameters);
        return $this->smarty->fetch('string:' . $templateString);
    }

    public function test_a_link_is_escaped_automatically()
    {
        $str = '<a>hello</a>';
        $this->assertEquals(
            '&lt;a&gt;hello&lt;/a&gt;',
            $this->escapeTemplateLocationComments(
                $this->render('{$str}', ['str' => $str])
            )
        );
    }

    public function test_nofilter_prevents_escape()
    {
        $str = '<a>hello</a>';
        $this->assertEquals(
            $str,
            $this->escapeTemplateLocationComments(
                $this->render('{$str nofilter}', ['str' => $str])
            )
        );
    }

    public function test_html_is_not_escaped_twice()
    {
        $str = '<a>hello</a>';
        $this->assertEquals(
            '&lt;a&gt;hello&lt;/a&gt;',
            $this->escapeTemplateLocationComments(
                $this->render('{$str|escape:"html"}', ['str' => $str])
            )
        );
    }

    private function escapeTemplateLocationComments($string)
    {
        return preg_replace('/\\n<!--(.|\s)*?-->\\n/', '', $string);
    }
}
