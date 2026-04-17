<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Handles identifiable object form and delegates form data saving to data handler.
 */
final class FormHandler implements FormHandlerInterface
{
    /**
     * @var FormDataHandlerInterface
     */
    private $dataHandler;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var bool
     */
    private $isDemoModeEnabled;

    /**
     * @param FormDataHandlerInterface $dataHandler
     * @param HookDispatcherInterface $hookDispatcher
     * @param TranslatorInterface $translator
     * @param bool $isDemoModeEnabled
     */
    public function __construct(
        FormDataHandlerInterface $dataHandler,
        HookDispatcherInterface $hookDispatcher,
        TranslatorInterface $translator,
        $isDemoModeEnabled
    ) {
        $this->dataHandler = $dataHandler;
        $this->hookDispatcher = $hookDispatcher;
        $this->translator = $translator;
        $this->isDemoModeEnabled = $isDemoModeEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(FormInterface $form)
    {
        return $this->handleForm($form);
    }

    /**
     * {@inheritdoc}
     */
    public function handleFor($id, FormInterface $form)
    {
        return $this->handleForm($form, $id);
    }

    /**
     * @param FormInterface $form
     * @param int|null $id
     *
     * @return FormHandlerResultInterface
     */
    private function handleForm(FormInterface $form, $id = null)
    {
        if (!$form->isSubmitted()) {
            return FormHandlerResult::createNotSubmitted();
        }

        if ($this->isDemoModeEnabled) {
            $form->addError(
                new FormError(
                    $this->translator->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error')
                )
            );

            return FormHandlerResult::createSubmittedButNotValid();
        }

        if (!$form->isValid()) {
            return FormHandlerResult::createSubmittedButNotValid();
        }

        if (null !== $id) {
            return $this->handleFormUpdate($form, $id);
        }

        return $this->handleFormCreate($form);
    }

    /**
     * @param FormInterface $form
     * @param int $id
     *
     * @return FormHandlerResultInterface
     */
    private function handleFormUpdate(FormInterface $form, $id)
    {
        $data = $form->getData();

        $this->hookDispatcher->dispatchWithParameters('actionBeforeUpdate' . Container::camelize($form->getName()) . 'FormHandler', [
            'form_data' => &$data,
            'id' => $id,
        ]);

        $newId = $this->dataHandler->update($id, $data);

        $this->hookDispatcher->dispatchWithParameters('actionAfterUpdate' . Container::camelize($form->getName()) . 'FormHandler', [
            'id' => $id,
            'form_data' => &$data,
        ]);

        return FormHandlerResult::createWithId($newId ?? $id);
    }

    /**
     * @param FormInterface $form
     *
     * @return FormHandlerResult
     */
    private function handleFormCreate(FormInterface $form)
    {
        $data = $form->getData();

        $this->hookDispatcher->dispatchWithParameters(
            'actionBeforeCreate' . Container::camelize($form->getName()) . 'FormHandler', [
                'form_data' => &$data,
            ]
        );

        $id = $this->dataHandler->create($data);

        $this->hookDispatcher->dispatchWithParameters('actionAfterCreate' . Container::camelize($form->getName()) . 'FormHandler', [
            'id' => $id,
            'form_data' => &$data,
        ]);

        return FormHandlerResult::createWithId($id);
    }
}
