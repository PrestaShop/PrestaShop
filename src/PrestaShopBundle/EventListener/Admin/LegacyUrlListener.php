<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\EventListener\Admin;

use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopBundle\Routing\Converter\LegacyUrlConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Converts any legacy url into a migrated Symfony url (if it exists) and redirect to it.
 */
class LegacyUrlListener
{
    /**
     * @var LegacyUrlConverter
     */
    private $converter;

    /**
     * @param LegacyUrlConverter $converter
     */
    public function __construct(LegacyUrlConverter $converter)
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

        try {
            $convertedUrl = $this->converter->convertByRequest($event->getRequest());
        } catch (CoreException) {
            return;
        }

        $event->setResponse(new RedirectResponse($convertedUrl, Response::HTTP_PERMANENTLY_REDIRECT));
    }
}
