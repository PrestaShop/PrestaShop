<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Improve\International\Translations;

use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormInterface;

/**
 * The module selector only applies to the "modules" translation type and its row is hidden for every
 * other one. While it was required, it rendered a required select whose placeholder option is empty, so
 * the browser refused to submit the form and reported nothing, the control it would have pointed at
 * being hidden. Choosing "Back office" and pressing Edit therefore did nothing at all.
 */
class ModifyTranslationsTypeTest extends KernelTestCase
{
    /** @var FormInterface */
    private $form;

    protected function setUp(): void
    {
        self::bootKernel();

        // Building the module choices reaches the module repository, which needs a shop and a language.
        /** @var ShopContextBuilder $shopContextBuilder */
        $shopContextBuilder = self::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop(1));
        $shopContextBuilder->setShopId(1);

        /** @var LanguageContextBuilder $languageContextBuilder */
        $languageContextBuilder = self::getContainer()->get('test_language_context_builder');
        $languageContextBuilder->setLanguageId(1);
        $languageContextBuilder->setDefaultLanguageId(1);

        /** @var FormHandlerInterface $formHandler */
        $formHandler = self::getContainer()->get('prestashop.admin.translations_settings.modify_translations.form_handler');
        $this->form = $formHandler->getForm();
    }

    public function testTheModuleSelectorIsNotRequired(): void
    {
        self::assertFalse(
            $this->form->get('module')->isRequired(),
            'the module selector does not apply to every translation type, so it must not block submission'
        );
    }

    /**
     * A required select is only harmless while something else has a value; this one starts on an empty
     * placeholder, which is what made the requirement impossible to satisfy without touching the field.
     */
    public function testTheModuleSelectorStillOffersAnEmptyPlaceholder(): void
    {
        $config = $this->form->get('module')->getConfig();

        self::assertSame('---', $config->getOption('placeholder'));
        self::assertNull($this->form->get('module')->getData(), 'nothing is preselected');
    }

    /**
     * The row is hidden by default, which is the other half of why the browser could not report it.
     */
    public function testTheModuleRowIsHiddenByDefault(): void
    {
        $rowAttr = $this->form->get('module')->getConfig()->getOption('row_attr');

        self::assertArrayHasKey('class', $rowAttr);
        self::assertStringContainsString('d-none', $rowAttr['class']);
    }

    /**
     * And the field that is always visible keeps its requirement, so this did not simply relax the form.
     */
    public function testTheAlwaysVisibleLanguageSelectorStaysRequired(): void
    {
        self::assertTrue($this->form->get('language')->isRequired());
    }
}
