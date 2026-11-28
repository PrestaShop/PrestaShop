<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShopBundle\Form\Admin\AdvancedParameters\Performance\CachingType;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class CachingTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return [
            new PreloadedExtension([new CachingType($translator, [])], []),
        ];
    }

    /**
     * The two systems the Symfony container caches through have to be offered on the adapter's
     * own answer: a loaded extension is not a usable one (memcached must be >= 3.1.6, apcu needs
     * apc.enabled=1), and selecting a system whose adapter refuses to be built leaves every
     * Symfony page - this one included - unable to boot.
     */
    public function testTheMemcachedOptionIsOfferedExactlyWhenItsAdapterSupportsIt(): void
    {
        $this->assertSame(MemcachedAdapter::isSupported(), $this->isOffered('CacheMemcached'));
    }

    public function testTheApcOptionIsOfferedExactlyWhenItsAdapterSupportsIt(): void
    {
        $this->assertSame(ApcuAdapter::isSupported(), $this->isOffered('CacheApc'));
    }

    /**
     * The remaining system backs no Symfony adapter, so loading the extension stays the only thing
     * that can be tested for it.
     */
    public function testASystemWithNoSymfonyAdapterStillGoesByTheExtension(): void
    {
        $this->assertSame(extension_loaded('memcache'), $this->isOffered('CacheMemcache'));
    }

    /**
     * Xcache is not proposed any more: its last release supports PHP 5.6, so no version this
     * release runs on can have it, and offering it put a "go and install this" link in front of
     * merchants for something they can never install.
     */
    public function testXcacheIsNotOffered(): void
    {
        $this->assertSame(
            ['CacheMemcache', 'CacheMemcached', 'CacheApc'],
            $this->offeredSystems()
        );
    }

    public function testAnUnavailableSystemIsDisabledAndSaysHowToInstallIt(): void
    {
        foreach ($this->offeredSystems() as $system) {
            $option = $this->option($system);

            if (!isset($option->vars['attr']['disabled'])) {
                continue;
            }

            $this->assertTrue($option->vars['attr']['disabled']);
            $this->assertStringContainsString('you must install', $option->vars['label']);

            return;
        }

        $this->markTestSkipped('Every caching system is available here, so the disabled path cannot be reached.');
    }

    /**
     * Whether an option is disabled and whether its label tells the merchant to install the
     * extension are decided by two separate closures, so they have to reach the same answer for
     * every system - otherwise the form asks you to install something on an option you can still
     * select, or silently disables one that looks fine.
     */
    public function testTheDisabledStateAndTheInstallMessageAlwaysAgree(): void
    {
        foreach ($this->offeredSystems() as $system) {
            $option = $this->option($system);

            // An available option keeps the plain choice as its label; an unavailable one swaps
            // in the install message, so a label that is no longer the choice means "unavailable".
            $this->assertSame(
                isset($option->vars['attr']['disabled']),
                $system !== $option->vars['label'],
                $system
            );
        }
    }

    /**
     * @return string[] the caching systems the form proposes, in the order it proposes them
     */
    private function offeredSystems(): array
    {
        $view = $this->factory->create(CachingType::class)->createView();

        return array_map(
            static fn (FormView $option): string => $option->vars['value'],
            $view['caching_system']->children
        );
    }

    private function isOffered(string $system): bool
    {
        return !isset($this->option($system)->vars['attr']['disabled']);
    }

    /**
     * Expanded choices are indexed by position, so the option is found by the value it submits.
     */
    private function option(string $system): FormView
    {
        $view = $this->factory->create(CachingType::class)->createView();

        foreach ($view['caching_system']->children as $option) {
            if ($system === $option->vars['value']) {
                return $option;
            }
        }

        $this->fail(sprintf('The caching system "%s" is not offered by the form at all.', $system));
    }
}
