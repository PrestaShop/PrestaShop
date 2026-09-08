<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Smarty;

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The legacy form helper escapes option labels because they can come from the database. That also
 * kills the markup a module legitimately puts in its own labels, so those labels go through this
 * modifier instead: markup survives, anything dangerous does not.
 */
class PurifyModifierTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $bootedKernel = self::bootKernel();
        // A real admin request assigns the kernel globally, which is where SymfonyContainer reads it
        // from; a bare KernelTestCase does not, so the modifier would take its fallback branch.
        global $kernel;
        $kernel = $bootedKernel;
    }

    /**
     * @dataProvider getLabels
     */
    public function testTheLabelKeepsItsMarkupWithoutWhatCouldAttackThePage(string $label, string $expectedToSurvive, ?string $expectedToDisappear): void
    {
        $purified = smarty_modifier_purify($label);

        $this->assertStringContainsString($expectedToSurvive, $purified);

        if ($expectedToDisappear !== null) {
            $this->assertStringNotContainsString($expectedToDisappear, $purified);
        }
    }

    public function getLabels(): iterable
    {
        yield 'plain text is untouched' => ['Delivery', 'Delivery', null];
        yield 'emphasis survives' => ['<strong>Recommended</strong> option', '<strong>Recommended</strong>', null];
        yield 'link survives' => ['See <a href="https://example.com">the guide</a>', '<a href="https://example.com">', null];
        yield 'script is removed' => ['Label<script>alert(1)</script>', 'Label', '<script'];
        yield 'inline handler is removed' => ['<span onclick="alert(1)">Label</span>', 'Label', 'onclick'];
        yield 'javascript href is removed' => ['<a href="javascript:alert(1)">Label</a>', 'Label', 'javascript:'];
    }

    public function testANullLabelBecomesAnEmptyString(): void
    {
        $this->assertSame('', smarty_modifier_purify(null));
    }

    public function testTheLabelIsEscapedWhenThereIsNoContainerToPurifyWith(): void
    {
        global $kernel;
        $bootedKernel = $kernel;
        $kernel = null;
        SymfonyContainer::resetStaticCache();

        try {
            $this->assertSame('&lt;strong&gt;Label&lt;/strong&gt;', smarty_modifier_purify('<strong>Label</strong>'));
        } finally {
            $kernel = $bootedKernel;
            SymfonyContainer::resetStaticCache();
        }
    }
}
