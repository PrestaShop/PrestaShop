<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Translation\Storage\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\CatalogueDomainConverter;
use Symfony\Component\Translation\MessageCatalogue;

class CatalogueDomainConverterTest extends TestCase
{
    public function testItNormalizesDottedDomainsAndKeepsOnlyMatchingOnes(): void
    {
        $source = new MessageCatalogue('en-US');
        $source->set('Video link', 'Video link', 'Modules.Demoextrafield.Admin');
        $source->set('Foreign label', 'Foreign label', 'Modules.Othermodule.Admin');

        $result = (new CatalogueDomainConverter())->normalizeAndFilter($source, ['#^ModulesDemoextrafield([A-Z]|\.|$)#']);

        $this->assertSame(['ModulesDemoextrafieldAdmin'], $result->getDomains());
        $this->assertSame('Video link', $result->get('Video link', 'ModulesDemoextrafieldAdmin'));
    }

    public function testItReturnsAnEmptyCatalogueWhenNothingMatches(): void
    {
        $source = new MessageCatalogue('en-US');
        $source->set('Foreign label', 'Foreign label', 'Modules.Othermodule.Admin');

        $result = (new CatalogueDomainConverter())->normalizeAndFilter($source, ['#^ModulesDemoextrafield([A-Z]|\.|$)#']);

        $this->assertSame([], $result->getDomains());
        $this->assertSame('en-US', $result->getLocale());
    }

    public function testItRemovesOnlyTheFirstTwoDots(): void
    {
        // third-level domains can carry a dot in their last segment, which must be preserved
        $source = new MessageCatalogue('en-US');
        $source->set('Hello', 'Hello', 'Modules.Demoextrafield.Some.Thing');

        $result = (new CatalogueDomainConverter())->normalizeAndFilter($source, ['#^ModulesDemoextrafield([A-Z]|\.|$)#']);

        $this->assertSame(['ModulesDemoextrafieldSome.Thing'], $result->getDomains());
        $this->assertSame('Hello', $result->get('Hello', 'ModulesDemoextrafieldSome.Thing'));
    }
}
