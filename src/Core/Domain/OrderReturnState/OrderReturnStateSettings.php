<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState;

class OrderReturnStateSettings
{
    /**
     * Order Return State name max length as defined in the ObjectModel
     */
    public const NAME_MAX_LENGTH = 64;

    /**
     * Seeded id of the "Waiting for confirmation" state — default value of order_return.state on creation.
     */
    public const STATE_WAITING_FOR_CONFIRMATION = 1;

    /**
     * Seeded id of the "Waiting for package" state — the only state that exposes the PDF download
     * in the merchant edit page (legacy AdminReturnController only renders the PDF link when state == 2).
     */
    public const STATE_WAITING_FOR_PACKAGE = 2;

    /**
     * Seeded id of the "Package received" state.
     */
    public const STATE_PACKAGE_RECEIVED = 3;

    /**
     * Seeded id of the "Return denied" state — hard-coded as the "denied" filter in the customer-facing list.
     */
    public const STATE_RETURN_DENIED = 4;

    /**
     * Seeded id of the "Return completed" state.
     */
    public const STATE_RETURN_COMPLETED = 5;
}
