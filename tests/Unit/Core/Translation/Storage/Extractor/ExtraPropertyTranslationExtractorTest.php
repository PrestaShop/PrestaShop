<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Translation\Storage\Extractor;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Extractor\ExtraPropertyTranslationExtractor;

class ExtraPropertyTranslationExtractorTest extends TestCase
{
    public function testItExtractsLabelAndDescriptionAsSeparateKeysUnderTheirDomain(): void
    {
        $extractor = $this->buildExtractor([
            new ExtraPropertyDefinition(
                'product',
                'video_link',
                labelWording: 'Video link',
                labelDomain: 'Modules.Demoextrafield.Admin',
                descriptionWording: 'Video URL per language',
                descriptionDomain: 'Modules.Demoextrafield.Admin',
            ),
        ]);

        $catalogue = $extractor->extract('en-US');

        $this->assertSame('en-US', $catalogue->getLocale());
        $this->assertSame(['Modules.Demoextrafield.Admin'], $catalogue->getDomains());
        // default catalogue convention: translation key == translation value
        $this->assertSame('Video link', $catalogue->get('Video link', 'Modules.Demoextrafield.Admin'));
        $this->assertSame('Video URL per language', $catalogue->get('Video URL per language', 'Modules.Demoextrafield.Admin'));
        $this->assertCount(2, $catalogue->all('Modules.Demoextrafield.Admin'));
    }

    public function testItKeepsWordingsFromDifferentDomainsSeparate(): void
    {
        $extractor = $this->buildExtractor([
            new ExtraPropertyDefinition('product', 'video_link', labelWording: 'Video link', labelDomain: 'Modules.Demoextrafield.Admin'),
            new ExtraPropertyDefinition('cms', 'revision_code', labelWording: 'Revision code', labelDomain: 'Modules.Other.Admin'),
        ]);

        $catalogue = $extractor->extract('en-US');

        $domains = $catalogue->getDomains();
        sort($domains);
        $this->assertSame(['Modules.Demoextrafield.Admin', 'Modules.Other.Admin'], $domains);
    }

    public function testItFallsBackToTheMessagesDomainForWordingsWithoutADomain(): void
    {
        $extractor = $this->buildExtractor([
            new ExtraPropertyDefinition(
                'customer',
                'credit_limit',
                labelWording: 'Credit limit',
                labelDomain: null,
                descriptionWording: 'Maximum customer credit amount',
                descriptionDomain: '',
            ),
        ]);

        $catalogue = $extractor->extract('en-US');

        // A wording without a paired domain falls back to Symfony's default "messages" domain.
        $this->assertSame(['messages'], $catalogue->getDomains());
        $this->assertSame('Credit limit', $catalogue->get('Credit limit', 'messages'));
        $this->assertSame('Maximum customer credit amount', $catalogue->get('Maximum customer credit amount', 'messages'));
    }

    public function testItSkipsAMissingLabelButKeepsTheDescription(): void
    {
        $extractor = $this->buildExtractor([
            new ExtraPropertyDefinition(
                'cms',
                'promo_banner',
                labelWording: null,
                labelDomain: 'Modules.Demoextrafield.Admin',
                descriptionWording: 'Translated promotional text displayed on the CMS page',
                descriptionDomain: 'Modules.Demoextrafield.Admin',
            ),
        ]);

        $catalogue = $extractor->extract('en-US');

        $this->assertSame(
            ['Translated promotional text displayed on the CMS page' => 'Translated promotional text displayed on the CMS page'],
            $catalogue->all('Modules.Demoextrafield.Admin')
        );
    }

    public function testItCollapsesDuplicateWordingsInTheSameDomain(): void
    {
        $extractor = $this->buildExtractor([
            new ExtraPropertyDefinition('product', 'field_a', labelWording: 'Shared label', labelDomain: 'Modules.Demoextrafield.Admin'),
            new ExtraPropertyDefinition('product', 'field_b', labelWording: 'Shared label', labelDomain: 'Modules.Demoextrafield.Admin'),
        ]);

        $catalogue = $extractor->extract('en-US');

        $this->assertCount(1, $catalogue->all('Modules.Demoextrafield.Admin'));
    }

    public function testItBuildsTheCatalogueOnlyOncePerLocale(): void
    {
        $repository = $this->createMock(ExtraPropertyDefinitionRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('getAllDefinitions')
            ->willReturn(new ExtraPropertyDefinitionCollection([]));

        $extractor = new ExtraPropertyTranslationExtractor($repository);

        $extractor->extract('en-US');
        $extractor->extract('en-US');
    }

    /**
     * @param list<ExtraPropertyDefinition> $definitions
     */
    private function buildExtractor(array $definitions): ExtraPropertyTranslationExtractor
    {
        $repository = $this->createMock(ExtraPropertyDefinitionRepositoryInterface::class);
        $repository->method('getAllDefinitions')
            ->willReturn(new ExtraPropertyDefinitionCollection($definitions));

        return new ExtraPropertyTranslationExtractor($repository);
    }
}
