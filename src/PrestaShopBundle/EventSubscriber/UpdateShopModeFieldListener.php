<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\EventSubscriber;

use PrestaShopBundle\Service\Form\ImprovedB2bTabsToggler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;

class UpdateShopModeFieldListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly ImprovedB2bTabsToggler $toggler,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SET_DATA => 'onPostSubmit',
        ];
    }

    public function onPostSubmit(): void
    {
        $this->toggler->sync();
    }
}
