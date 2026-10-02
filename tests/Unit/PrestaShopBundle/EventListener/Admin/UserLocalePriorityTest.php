<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\EventListener\Admin\UserLocaleSubscriber;
use Symfony\Component\HttpKernel\EventListener\LocaleAwareListener;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * UserLocaleSubscriber sets the employee locale on the Request; LocaleAwareListener is what copies
 * that locale into the kernel.locale_aware services, the translator included. The first has to run
 * before the second.
 *
 * Priority is the only thing that orders them. At equal priority the dispatcher falls back to the
 * order listeners were registered in the compiled container, which no code controls - so this test
 * pins the relation rather than the number.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/42149
 */
class UserLocalePriorityTest extends TestCase
{
    public function testItRunsStrictlyBeforeTheListenerThatPropagatesTheLocale(): void
    {
        $this->assertGreaterThan(
            $this->getLocaleAwareListenerPriority(),
            UserLocaleSubscriber::USER_LOCALE_PRIORITY,
            'UserLocaleSubscriber must outrank LocaleAwareListener, otherwise the translator can be '
            . 'left on the previous locale for the whole request'
        );
    }

    public function testTheSubscriberStillRegistersOnKernelRequestAtThatPriority(): void
    {
        $events = UserLocaleSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::REQUEST, $events);
        $this->assertSame(
            [['onKernelRequest', UserLocaleSubscriber::USER_LOCALE_PRIORITY]],
            $events[KernelEvents::REQUEST]
        );
    }

    /**
     * Read from Symfony rather than hardcoded, so the test keeps meaning if the framework moves it.
     */
    private function getLocaleAwareListenerPriority(): int
    {
        $subscriptions = LocaleAwareListener::getSubscribedEvents()[KernelEvents::REQUEST];

        // [['onKernelRequest', 15]]
        return (int) $subscriptions[0][1];
    }
}
