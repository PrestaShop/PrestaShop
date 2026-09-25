<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Context;
use HelperForm;
use HelperKpi;
use PHPUnit\Framework\TestCase;

/**
 * The helper templates are only shipped by the default back office theme, while Smarty resolves the
 * helper's relative path against whichever theme the current page uses - and on a Symfony page, such as
 * the order view where displayAdminOrderTop is rendered, against no admin theme at all. A module calling
 * HelperForm from an admin hook therefore hit "Unable to load template 'file:helpers/form/form.tpl'".
 */
class HelperTemplateFallbackTest extends TestCase
{
    /** @var array */
    private $originalTemplateDirs;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalTemplateDirs = Context::getContext()->smarty->getTemplateDir();
    }

    protected function tearDown(): void
    {
        Context::getContext()->smarty->setTemplateDir($this->originalTemplateDirs);
        parent::tearDown();
    }

    public function testFormTemplateResolvesOnAPageThatConfiguresNoAdminTheme(): void
    {
        // What a Symfony back office page leaves behind: AdminController::initSmarty never ran.
        Context::getContext()->smarty->setTemplateDir([_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'templates']);

        $template = (new HelperForm())->createTemplate('form.tpl');

        self::assertTrue($template->source->exists);
    }

    public function testFormTemplateResolvesWhenTheCurrentThemeDoesNotShipIt(): void
    {
        Context::getContext()->smarty->setTemplateDir([$this->themeTemplateDir('new-theme')]);

        $template = (new HelperForm())->createTemplate('form.tpl');

        self::assertTrue($template->source->exists);
        self::assertStringContainsString(
            $this->themeTemplateDir('default'),
            $template->source->filepath
        );
    }

    /**
     * The fallback must not shadow a theme that does provide the template - new-theme ships the KPI
     * helper but not the form one, so it is the case that tells the two apart.
     */
    public function testAThemeThatShipsTheTemplateIsStillPreferred(): void
    {
        Context::getContext()->smarty->setTemplateDir([$this->themeTemplateDir('new-theme')]);

        $template = (new HelperKpi())->createTemplate('kpi.tpl');

        self::assertTrue($template->source->exists);
        self::assertStringContainsString(
            $this->themeTemplateDir('new-theme'),
            $template->source->filepath
        );
    }

    public function testTheDefaultThemeKeepsResolvingFromItsOwnTemplates(): void
    {
        Context::getContext()->smarty->setTemplateDir([$this->themeTemplateDir('default')]);

        $template = (new HelperForm())->createTemplate('form.tpl');

        self::assertTrue($template->source->exists);
        self::assertStringContainsString(
            $this->themeTemplateDir('default'),
            $template->source->filepath
        );
    }

    private function themeTemplateDir(string $theme): string
    {
        return _PS_BO_ALL_THEMES_DIR_ . $theme . DIRECTORY_SEPARATOR . 'template';
    }
}
