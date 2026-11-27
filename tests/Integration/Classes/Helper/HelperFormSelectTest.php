<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Helper;

use Context;
use HelperForm;
use PHPUnit\Framework\TestCase;

class HelperFormSelectTest extends TestCase
{
    private array $templateDirs;

    protected function setUp(): void
    {
        parent::setUp();

        $smarty = Context::getContext()->smarty;
        $this->templateDirs = $smarty->getTemplateDir();
        $smarty->setTemplateDir(_PS_ROOT_DIR_ . '/admin-dev/themes/default/template');
    }

    protected function tearDown(): void
    {
        Context::getContext()->smarty->setTemplateDir($this->templateDirs);

        parent::tearDown();
    }

    /**
     * @dataProvider getSelectFields
     */
    public function testSelectFieldRendersOptionsAndSelection(array $options, bool $multiple, mixed $value, array $expectedSelection): void
    {
        $input = [
            'type' => 'select',
            'label' => 'Choice',
            'name' => 'choice',
            'options' => [
                'query' => $options,
                'id' => 'id',
                'name' => 'name',
            ],
        ];
        if ($multiple) {
            $input['multiple'] = true;
        }

        $helper = new HelperForm();
        // HelperForm suffixes the name of a multiple select with []
        $helper->fields_value = [$multiple ? 'choice[]' : 'choice' => $value];
        $html = $helper->generateForm([['form' => ['input' => [$input]]]]);

        preg_match_all('#<option value="(\d+)"([^>]*)>\s*([^<]*?)\s*</option>#', $html, $matches, PREG_SET_ORDER);
        $selection = [];
        foreach ($matches as $match) {
            $selection[$match[3]] = str_contains($match[2], 'selected="selected"');
        }

        $this->assertSame($expectedSelection, $selection);
    }

    public static function getSelectFields(): iterable
    {
        $arrays = [
            ['id' => 7, 'name' => 'First option'],
            ['id' => 9, 'name' => 'Chosen option'],
        ];
        $objects = array_map(static fn (array $option): object => (object) $option, $arrays);
        $expectedSelection = ['First option' => false, 'Chosen option' => true];

        yield 'array options, single selection' => [$arrays, false, 9, $expectedSelection];
        yield 'object options, single selection' => [$objects, false, 9, $expectedSelection];
        yield 'array options, multiple selection' => [$arrays, true, [9], $expectedSelection];
        yield 'object options, multiple selection' => [$objects, true, [9], $expectedSelection];
    }
}
