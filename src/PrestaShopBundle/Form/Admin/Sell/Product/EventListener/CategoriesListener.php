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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\EventListener;

use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CategoriesListener implements EventSubscriberInterface
{
    /**
     * @var FormCloner
     */
    private $formCloner;

    /**
     * @param FormCloner $formCloner
     */
    public function __construct(
        FormCloner $formCloner
    ) {
        $this->formCloner = $formCloner;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'updateDefaultCategoryChoices',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function updateDefaultCategoryChoices(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (empty($data)) {
            return;
        }

        // Update choices list to contain all selected categories, because extra choice might have been added by javascript
        $newChoicesForm = $this->formCloner->cloneForm($form->get('default_category_id'), [
            'choices' => $this->formatNewChoices($data),
        ]);

        $form->add($newChoicesForm);
    }

    private function formatNewChoices(array $data): array
    {
        $choices = [];
        foreach ($data['product_categories'] as $category) {
            $choices[$category['display_name']] = $category['id'];
        }

        return $choices;
    }
}
