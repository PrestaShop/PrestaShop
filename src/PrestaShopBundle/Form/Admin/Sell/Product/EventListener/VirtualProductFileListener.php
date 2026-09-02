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

class VirtualProductFileListener implements EventSubscriberInterface
{
    /**
     * @var FormCloner
     */
    private $formCloner;

    public function __construct(
        FormCloner $formCloner
    ) {
        $this->formCloner = $formCloner;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'adaptFormConstraints',
        ];
    }

    /**
     * Remove form constraints if there is no virtual file added, to avoid invalidating the form for nothing
     *
     * @param FormEvent $event
     */
    public function adaptFormConstraints(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        $isAddingFile = !empty($data['has_file']) && empty($data['virtual_product_file_id']);
        $onlyUpdatingFileSettings = !empty($data['has_file']) && !empty($data['virtual_product_file_id']) && empty($data['file']);

        if ($isAddingFile) {
            // when new file is being added we leave all constraints unchanged
            return;
        }

        if ($onlyUpdatingFileSettings) {
            // when existing file is being updated we do not require uploading a file (remove file NotBlank constraints),
            // but leave constraints for other updatable fields
            $form->add($this->formCloner->cloneForm($form->get('file'), ['constraints' => []]));

            return;
        }

        // when existing file is being deleted or file is not being added (has_file is falsy) we remove all constraints
        $form->add($this->formCloner->cloneForm($form->get('file'), ['constraints' => []]));
        $form->add($this->formCloner->cloneForm($form->get('name'), ['constraints' => []]));
        $form->add($this->formCloner->cloneForm($form->get('access_days_limit'), ['constraints' => []]));
        $form->add($this->formCloner->cloneForm($form->get('download_times_limit'), ['constraints' => []]));
    }
}
