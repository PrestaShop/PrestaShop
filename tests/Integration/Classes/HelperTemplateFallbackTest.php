<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Context;
use Group;
use HelperForm;
use HelperKpi;
use Language;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Twig\Layout\PrestaShopLayoutGlobalVariables;
use PrestaShopBundle\Twig\Layout\SmartyVariablesFiller;
use PrestaShopBundle\Twig\Layout\TemplateVariables;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HelperTemplateFallbackTest extends KernelTestCase
{
    /**
     * @var array<int|string, string>
     */
    private array $originalTemplateDirs;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->originalTemplateDirs = Context::getContext()->smarty->getTemplateDir();

        // What a Symfony back office page sets up before rendering legacy hooks
        (new PrestaShopLayoutGlobalVariables(
            self::getContainer()->get(LegacyContext::class),
            $this->createMock(TemplateVariables::class),
            $this->createMock(SmartyVariablesFiller::class)
        ))->setupSmarty('Title', 'Title', false);
    }

    protected function tearDown(): void
    {
        Context::getContext()->smarty->setTemplateDir($this->originalTemplateDirs);
        parent::tearDown();
    }

    public function testAHelperFormWithAGroupInputRendersOnASymfonyPage(): void
    {
        $languageId = (int) Context::getContext()->language->id;
        $helper = new HelperForm();
        $helper->token = 'token';
        $helper->currentIndex = 'index.php';
        $helper->default_form_language = $languageId;
        $helper->languages = array_map(
            static fn (array $language): array => $language + ['is_default' => (int) $language['id_lang'] === $languageId],
            Language::getLanguages(false)
        );
        $groups = Group::getGroups($languageId);
        $helper->fields_value = [];
        foreach ($groups as $group) {
            $helper->fields_value['groupBox_' . $group['id_group']] = false;
        }

        $html = $helper->generateForm([[
            'form' => [
                'legend' => ['title' => 'Probe'],
                'input' => [[
                    'type' => 'group',
                    'label' => 'Groups',
                    'name' => 'groupBox',
                    'values' => $groups,
                ]],
            ],
        ]]);

        self::assertStringContainsString('groupBox', $html);
    }

    public function testATemplateTheNewThemeShipsIsStillTakenFromIt(): void
    {
        $template = (new HelperKpi())->createTemplate('kpi.tpl');

        self::assertStringContainsString(_PS_BO_ALL_THEMES_DIR_ . 'new-theme', $template->source->filepath);
    }
}
