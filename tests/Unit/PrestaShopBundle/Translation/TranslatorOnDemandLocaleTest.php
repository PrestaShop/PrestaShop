<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Translation;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\TranslatorComponent;
use Symfony\Component\Translation\Loader\ArrayLoader;

/**
 * Catalogues are registered for the translator's own locale only, so asking trans() for a wording in
 * another language used to return the wording itself: the $locale argument silently did nothing
 * outside the Symfony container, which is every front office and CLI context.
 */
class TranslatorOnDemandLocaleTest extends TestCase
{
    /**
     * @var string[] locales the loader was asked to register
     */
    private array $requested = [];

    private function buildTranslator(): TranslatorComponent
    {
        $this->requested = [];
        $translator = new TranslatorComponent('en-US');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', ['Save' => 'Save'], 'en-US', 'AdminGlobal');
        $translator->setLanguageLoader(function (string $locale) use ($translator): void {
            $this->requested[] = $locale;
            $translator->addResource('array', ['Save' => 'Enregistrer'], $locale, 'AdminGlobal');
        });

        return $translator;
    }

    public function testAnExplicitLocaleIsLoadedSoItsTranslationIsReturned(): void
    {
        $translator = $this->buildTranslator();

        $this->assertSame('Enregistrer', $translator->trans('Save', [], 'Admin.Global', 'fr-FR'));
        $this->assertSame(['fr-FR'], $this->requested);
    }

    public function testTheDefaultPathNeverAsksForAnotherLocale(): void
    {
        $translator = $this->buildTranslator();

        $this->assertSame('Save', $translator->trans('Save', [], 'Admin.Global'));
        $this->assertSame([], $this->requested);
    }

    /**
     * Registering resources makes Symfony drop the cached catalogue, so repeating the request must
     * not rebuild it on every lookup.
     */
    public function testALocaleIsOnlyRequestedOnce(): void
    {
        $translator = $this->buildTranslator();

        $translator->trans('Save', [], 'Admin.Global', 'fr-FR');
        $translator->trans('Save', [], 'Admin.Global', 'fr-FR');
        $translator->trans('Save', [], 'Admin.Global', 'fr-FR');

        $this->assertSame(['fr-FR'], $this->requested);
    }

    public function testAnAlreadyLoadedLocaleIsNotRequested(): void
    {
        $translator = $this->buildTranslator();

        // Force the en-US catalogue to exist, as it does for the translator's own locale.
        $translator->trans('Save', [], 'Admin.Global');

        $this->assertSame('Save', $translator->trans('Save', [], 'Admin.Global', 'en-US'));
        $this->assertSame([], $this->requested);
    }
}
