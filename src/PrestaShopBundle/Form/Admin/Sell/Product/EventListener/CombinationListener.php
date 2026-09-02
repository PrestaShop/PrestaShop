<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CombinationListener implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'adaptCombinationForm',
            FormEvents::PRE_SUBMIT => 'adaptCombinationForm',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function adaptCombinationForm(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if ($form->has('stock')) {
            $stock = $form->get('stock');
            if ($stock->has('quantities')) {
                $quantities = $stock->get('quantities');
                if ($quantities->has('stock_movements') && empty($data['stock']['quantities']['stock_movements'])) {
                    $quantities->remove('stock_movements');
                }
            }
        }
    }
}
