<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Category\EventListener;

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

        // Adapt target options
        $targetOptions['entity_type'] = 'category';
        $targetOptions['label'] = $this->getEntityAttribute($targetOptions, 'label');
        $targetOptions['placeholder'] = $this->getEntityAttribute($targetOptions, 'placeholder');
        $targetOptions['help'] = $this->getEntityAttribute($targetOptions, 'help');
        $targetOptions['remote_url'] = $this->getEntityAttribute($targetOptions, 'search-url');
        $targetOptions['filtered_identities'] = json_decode($this->getEntityAttribute($targetOptions, 'filtered'));
        if (RedirectType::TYPE_NOT_FOUND === $dataType || RedirectType::TYPE_GONE === $dataType ||
            RedirectType::TYPE_DEFAULT === $dataType || RedirectType::TYPE_GONE_DISPLAYED === $dataType ||
            RedirectType::TYPE_SUCCESS_DISPLAYED === $dataType || RedirectType::TYPE_NOT_FOUND_DISPLAYED === $dataType) {
            $targetOptions['row_attr']['class'] = 'd-none';
        }

        // Replace existing field with new one with adapted options
        $cloner = new FormCloner();
        $clonedForm = $cloner->cloneForm($targetField, $targetOptions);
        $form->add($clonedForm);
    }

    /**
     * @param array $targetOptions
     * @param string $attributeName
     *
     * @return string
     */
    private function getEntityAttribute(array $targetOptions, string $attributeName): string
    {
        $dataAttribute = sprintf('data-%s', $attributeName);

        return $targetOptions['attr'][$dataAttribute] ?? '';
    }
}
