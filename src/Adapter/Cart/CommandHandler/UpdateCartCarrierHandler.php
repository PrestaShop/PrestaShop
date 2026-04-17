<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Carrier;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateCartCarrierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
final class UpdateCartCarrierHandler extends AbstractCartHandler implements UpdateCartCarrierHandlerInterface
{
    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @param ContextStateManager $contextStateManager
     */
    public function __construct(ContextStateManager $contextStateManager)
    {
        $this->contextStateManager = $contextStateManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCartCarrierCommand $command)
    {
        $this->assertActiveCarrier($command->getNewCarrierId());

        $cart = $this->getCart($command->getCartId());
        $this->setCartContext($this->contextStateManager, $cart);

        try {
            $cart->setDeliveryOption(
                [(int) $cart->id_address_delivery => $this->formatLegacyDeliveryOptionFromCarrierId($command->getNewCarrierId())],
                true,
            );
            $cart->id_carrier = $command->getNewCarrierId();
            $cart->update();
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }
    }

    /**
     * @param int $carrierId
     *
     * @throws CartConstraintException
     */
    private function assertActiveCarrier(int $carrierId): void
    {
        if (0 === $carrierId) {
            return;
        }

        $carrier = new Carrier($carrierId);

        if (!Validate::isLoadedObject($carrier) || (int) $carrier->id !== $carrierId) {
            throw new CartConstraintException(sprintf('Carrier with id "%d" was not found', $carrierId), CartConstraintException::INVALID_CARRIER);
        }
        if (!$carrier->active) {
            throw new CartConstraintException(sprintf('Carrier with id "%d" is not active', $carrierId), CartConstraintException::INVALID_CARRIER);
        }
    }

    /**
     * Delivery option consists of deliveryAddress and carrierId.
     *
     * Legacy multishipping feature used comma separated carriers in delivery option (e.g. {'1':'6,7'}
     * Now that multishipping is gone - delivery option should consist of one carrier and one address.
     *
     * However the structure of deliveryOptions is still used with comma in legacy, so
     * this method provides assurance for deliveryOption structure until major refactoring
     *
     * @param int $carrierId
     *
     * @return string
     */
    private function formatLegacyDeliveryOptionFromCarrierId(int $carrierId): string
    {
        return sprintf('%d,', $carrierId);
    }
}
