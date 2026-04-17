<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
