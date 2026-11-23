<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order;

use Order;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrderTaxMethodProvider extends AbstractOrderHandler
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getTaxMethod(Order $order): string
    {
        return $this->isTaxIncludedInOrder($order) ?
            $this->translator->trans('Tax included', [], 'Admin.Global') :
            $this->translator->trans('Tax excluded', [], 'Admin.Global');
    }
}
