<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\API;

use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Wraps BulkCommandExceptionInterface in an HttpException with status 207
 * so that the FlattenException created by Symfony carries the correct HTTP status code.
 * The response body formatting is handled by BulkCommandExceptionNormalizer.
 */
class BulkCommandExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof BulkCommandExceptionInterface) {
            return;
        }

        $event->setThrowable(new HttpException(
            Response::HTTP_MULTI_STATUS,
            $exception->getMessage(),
            $exception
        ));
    }
}
