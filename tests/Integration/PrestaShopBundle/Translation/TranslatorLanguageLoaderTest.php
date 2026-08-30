<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Translation;

use PrestaShop\PrestaShop\Adapter\Module\Repository\ModuleRepository;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\ExtraPropertyTranslationExtractor;
use PrestaShopBundle\Translation\TranslatorComponent;
use PrestaShopBundle\Translation\TranslatorLanguageLoader;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Ensures TranslatorLanguageLoader injects the extra property registry wordings (which live in the
 * database, not in XLF files) into the runtime translator catalogue, with their domains normalized
 * to the catalogue convention. This is what lets Module::isUsingNewTranslationSystem() detect a
 * module whose only new-system wordings come from extra properties, in any locale.
 *
 * ./vendor/bin/phpunit -c tests/Integration/phpunit.xml --filter=TranslatorLanguageLoaderTest
 */
class TranslatorLanguageLoaderTest extends KernelTestCase
{
    public function testItInjectsExtraPropertyWordingsUnderNormalizedDomains(): void
    {
        // An unused locale keeps the core XLF scan empty so the test isolates the extra-property injection.
        $locale = 'zz-ZZ';

        $moduleRepository = $this->createMock(ModuleRepository::class);
        $moduleRepository->method('getPresentModulesPaths')->willReturn([]);

        $extractor = $this->createMock(ExtraPropertyTranslationExtractor::class);
        $extractor->method('extract')->willReturnCallback(function (string $extractLocale): MessageCatalogue {
            $catalogue = new MessageCatalogue($extractLocale);
            // Dotted domain, as declared by a module's extra property definition.
            $catalogue->set('Video link', 'Video link', 'Modules.Demoextrafield.Admin');

            return $catalogue;
        });

        $translator = new TranslatorComponent($locale);
        (new TranslatorLanguageLoader($moduleRepository, $extractor))
            ->setIsAdminContext(true)
            ->loadLanguage($translator, $locale, true);

        $catalogue = $translator->getCatalogue($locale);

        // Dots removed -> matches the catalogue convention and Module::isUsingNewTranslationSystem().
        $this->assertContains('ModulesDemoextrafieldAdmin', $catalogue->getDomains());
        $this->assertSame('Video link', $catalogue->get('Video link', 'ModulesDemoextrafieldAdmin'));
    }

    public function testItSkipsExtraPropertyWordingsWhenDatabaseIsDisabled(): void
    {
        $locale = 'zz-ZZ';

        $moduleRepository = $this->createMock(ModuleRepository::class);
        $moduleRepository->method('getPresentModulesPaths')->willReturn([]);

        $extractor = $this->createMock(ExtraPropertyTranslationExtractor::class);
        $extractor->expects($this->never())->method('extract');

        $translator = new TranslatorComponent($locale);
        (new TranslatorLanguageLoader($moduleRepository, $extractor))
            ->setIsAdminContext(true)
            ->loadLanguage($translator, $locale, false);

        $this->assertNotContains('ModulesDemoextrafieldAdmin', $translator->getCatalogue($locale)->getDomains());
    }
}
