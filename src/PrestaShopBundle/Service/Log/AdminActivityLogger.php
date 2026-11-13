<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Service\Log;

use PrestaShop\PrestaShop\Core\ActivityLog\AdminActivity;
use PrestaShop\PrestaShop\Core\ActivityLog\AdminActivityLoggerInterface;
use PrestaShop\PrestaShop\Core\ActivityLog\AdminActivityType;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * Logs successful Back Office activities through the standard logging infrastructure.
 */
final class AdminActivityLogger implements AdminActivityLoggerInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly AdminActivityScope $scope,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function log(AdminActivity $activity): void
    {
        try {
            if (!$this->scope->isEnabled()) {
                return;
            }

            $message = $this->getMessage($activity);
            if (null === $message) {
                return;
            }

            $this->logger->info(
                $message,
                [
                    'object_type' => $activity->getObjectType(),
                    'object_id' => $activity->getLogObjectId(),
                    'allow_duplicate' => true,
                ]
            );
        } catch (Throwable) {
            // Activity logging must never break a successful Back Office operation.
        }
    }

    private function getMessage(AdminActivity $activity): ?string
    {
        return match ($activity->getType()) {
            AdminActivityType::CREATE => $this->formatMessage(
                '%s addition',
                $activity->getObjectType()
            ),
            AdminActivityType::UPDATE => $this->formatMessage(
                '%s modification',
                $activity->getObjectType()
            ),
            AdminActivityType::DELETE => $this->formatMessage(
                '%s deletion',
                $activity->getObjectType()
            ),
            AdminActivityType::ACTIVATE => $this->getStatusMessage($activity, true),
            AdminActivityType::DEACTIVATE => $this->getStatusMessage($activity, false),
            AdminActivityType::DUPLICATE => $this->getDuplicateMessage($activity),
        };
    }

    private function getStatusMessage(AdminActivity $activity, bool $activated): ?string
    {
        if (null === $activity->getObjectId()) {
            return null;
        }

        return $this->formatMessage(
            $activated ? '%s activated: %d' : '%s deactivated: %d',
            $activity->getObjectType(),
            $activity->getObjectId()
        );
    }

    private function getDuplicateMessage(AdminActivity $activity): ?string
    {
        if (null === $activity->getObjectId() || null === $activity->getNewObjectId()) {
            return null;
        }

        return $this->formatMessage(
            '%s duplicated: (from %d to %d).',
            $activity->getObjectType(),
            $activity->getObjectId(),
            $activity->getNewObjectId()
        );
    }

    private function formatMessage(string $message, mixed ...$values): string
    {
        return sprintf(
            $this->translator->trans(
                $message,
                [],
                'Admin.Advparameters.Feature'
            ),
            ...$values
        );
    }
}
