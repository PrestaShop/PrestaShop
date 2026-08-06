<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Translation\Loader;

use Doctrine\DBAL\Exception as DBALException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\ExtraPropertyTranslationExtractor;
use PrestaShopBundle\Translation\Loader\ExtraPropertyTranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;

class ExtraPropertyTranslationLoaderTest extends TestCase
{
    public function testItExposesRegistryDomainsNormalizedToTheCatalogueConvention(): void
    {
        $loader = $this->buildLoader([
            'Modules.Demoextrafield.Admin' => ['Video link' => 'Video link'],
            'Modules.Other.Admin' => ['Code' => 'Code'],
        ]);

        $this->assertSame(
            ['ModulesDemoextrafieldAdmin', 'ModulesOtherAdmin'],
            $loader->getNormalizedDomains('en-US')
        );
    }

    public function testItLoadsTheWordingsOfTheRequestedNormalizedDomain(): void
    {
        $loader = $this->buildLoader([
            'Modules.Demoextrafield.Admin' => ['Video link' => 'Video link'],
            'Modules.Other.Admin' => ['Code' => 'Code'],
        ]);

        $catalogue = $loader->load('extra_property', 'en-US', 'ModulesDemoextrafieldAdmin');

        $this->assertSame(['ModulesDemoextrafieldAdmin'], $catalogue->getDomains());
        // Default-catalogue convention: key == value.
        $this->assertSame(['Video link' => 'Video link'], $catalogue->all('ModulesDemoextrafieldAdmin'));
    }

    public function testItMergesDottedDomainsThatNormalizeToTheSameCatalogueDomain(): void
    {
        // "Modules.Foo.Admin.Help" keeps its third-level dot once normalized, but a flat domain and a
        // sub-domain may still collapse together; all matching wordings must be merged.
        $loader = $this->buildLoader([
            'Modules.Demoextrafield.Admin' => ['Label A' => 'Label A'],
            'Modules.Demoextrafield.AdminExtra' => ['Label B' => 'Label B'],
        ]);

        $catalogue = $loader->load('extra_property', 'en-US', 'ModulesDemoextrafieldAdmin');

        $this->assertSame(['Label A' => 'Label A'], $catalogue->all('ModulesDemoextrafieldAdmin'));
    }

    public function testItReturnsAnEmptyCatalogueForAnUnknownDomain(): void
    {
        $loader = $this->buildLoader(['Modules.Demoextrafield.Admin' => ['Video link' => 'Video link']]);

        $catalogue = $loader->load('extra_property', 'en-US', 'ModulesUnknownAdmin');

        $this->assertSame([], $catalogue->all('ModulesUnknownAdmin'));
    }

    public function testItContributesNothingWhenTheDatabaseIsUnavailable(): void
    {
        // The translator catalogue is warmed by CLI commands (cache:clear) that may run without a
        // reachable database; a connection failure must not break the build.
        $extractor = $this->createMock(ExtraPropertyTranslationExtractor::class);
        $extractor->method('extract')->willThrowException(new DBALException('Access denied'));

        $loader = new ExtraPropertyTranslationLoader($extractor);

        $this->assertSame([], $loader->getNormalizedDomains('en-US'));
        $this->assertSame([], $loader->load('extra_property', 'en-US', 'ModulesDemoextrafieldAdmin')->all('ModulesDemoextrafieldAdmin'));
    }

    /**
     * @param array<string, array<string, string>> $messagesByDottedDomain
     */
    private function buildLoader(array $messagesByDottedDomain): ExtraPropertyTranslationLoader
    {
        $extractor = $this->createMock(ExtraPropertyTranslationExtractor::class);
        $extractor->method('extract')->willReturnCallback(
            function (string $locale) use ($messagesByDottedDomain): MessageCatalogue {
                $catalogue = new MessageCatalogue($locale);
                foreach ($messagesByDottedDomain as $domain => $messages) {
                    $catalogue->add($messages, $domain);
                }

                return $catalogue;
            }
        );

        return new ExtraPropertyTranslationLoader($extractor);
    }
}
