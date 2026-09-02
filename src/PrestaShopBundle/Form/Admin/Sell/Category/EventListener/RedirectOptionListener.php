<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Category\EventListener;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\RedirectType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Adapts the attribute of target input depending on which type has been selected, this
 * automatically adapts the label, placeholders and search url for entities.
 */
class RedirectOptionListener implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'updateRedirectionOptions',
            FormEvents::PRE_SUBMIT => 'updateRedirectionOptions',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function updateRedirectionOptions(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();
        $targetField = $form->get('target');
        $targetOptions = $targetField->getConfig()->getOptions();
        $dataType = $data['type'] ?? $form->get('type')->getConfig()->getOption('default_empty_data');
        if (RedirectType::TYPE_NOT_FOUND === $dataType || RedirectType::TYPE_GONE === $dataType) {
            $targetOptions['row_attr']['class'] = 'd-none';
        }

        // Replace existing field with new one with adapted options
        $cloner = new FormCloner();
        $clonedForm = $cloner->cloneForm($targetField, $targetOptions);
        $form->add($clonedForm);
    }
}
