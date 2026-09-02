<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\Admin;

use PrestaShopBundle\Routing\Converter\LegacyParametersConverter;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tools;

/**
 * This listener converts the request routing information (if present) into an array
 * of legacy parameters which is then injected into the Tools class allowing to access
 * former legacy parameters using the same Tools::getValue and the same parameter name.
 *
 * Note: this is limited to parameters defined in the routing via _legacy_link and _legacy_parameters
 */
class LegacyParametersListener
{
    /**
     * @var LegacyParametersConverter
     */
    private $converter;

    /**
     * @param LegacyParametersConverter $converter
     */
    public function __construct(LegacyParametersConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $legacyParameters = $this->converter->getParameters($request->attributes->all(), $request->query->all());
        if (null === $legacyParameters) {
            return;
        }

        Tools::setFallbackParameters($legacyParameters);
    }
}
