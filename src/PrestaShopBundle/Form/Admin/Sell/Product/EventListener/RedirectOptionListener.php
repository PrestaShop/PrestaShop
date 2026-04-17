<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\EventListener;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
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
        $dataType = $data['type'] ?? RedirectType::TYPE_NOT_FOUND;
        switch ($dataType) {
            case RedirectType::TYPE_CATEGORY_PERMANENT:
            case RedirectType::TYPE_CATEGORY_TEMPORARY:
                $entityType = 'category';
                break;
            case RedirectType::TYPE_PRODUCT_PERMANENT:
            case RedirectType::TYPE_PRODUCT_TEMPORARY:
            default:
                $entityType = 'product';
                break;
        }

        // Adapt target options
        $targetOptions['entity_type'] = $entityType;
        $targetOptions['label'] = $this->getEntityAttribute($targetOptions, $entityType, 'label');
        $targetOptions['placeholder'] = $this->getEntityAttribute($targetOptions, $entityType, 'placeholder');
        $targetOptions['help'] = $this->getEntityAttribute($targetOptions, $entityType, 'help');
        $targetOptions['remote_url'] = $this->getEntityAttribute($targetOptions, $entityType, 'search-url');
        $targetOptions['filtered_identities'] = json_decode($this->getEntityAttribute($targetOptions, $entityType, 'filtered'));
        if (RedirectType::TYPE_NOT_FOUND === $dataType || RedirectType::TYPE_GONE === $dataType
            || RedirectType::TYPE_DEFAULT === $dataType || RedirectType::TYPE_GONE_DISPLAYED === $dataType
            || RedirectType::TYPE_SUCCESS_DISPLAYED === $dataType || RedirectType::TYPE_NOT_FOUND_DISPLAYED === $dataType) {
            $targetOptions['row_attr']['class'] = 'd-none';
        }

        // Replace existing field with new one with adapted options
        $cloner = new FormCloner();
        $clonedForm = $cloner->cloneForm($targetField, $targetOptions);
        $form->add($clonedForm);
    }

    /**
     * @param array $targetOptions
     * @param string $entityType
     * @param string $attributeName
     *
     * @return string
     */
    private function getEntityAttribute(array $targetOptions, string $entityType, string $attributeName): string
    {
        $dataAttribute = sprintf('data-%s-%s', $entityType, $attributeName);

        return $targetOptions['attr'][$dataAttribute] ?? '';
    }
}
