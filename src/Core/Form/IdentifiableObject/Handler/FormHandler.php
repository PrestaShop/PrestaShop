<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler;

use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\CreatedApiClient;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertiesFormDataPersister;
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
     * @var ExtraPropertiesFormDataPersister
     */
    private $extraPropertiesFormDataPersister;

    /**
     * @param FormDataHandlerInterface $dataHandler
     * @param HookDispatcherInterface $hookDispatcher
     * @param TranslatorInterface $translator
     * @param bool $isDemoModeEnabled
     * @param ExtraPropertiesFormDataPersister $extraPropertiesFormDataPersister
     */
    public function __construct(
        FormDataHandlerInterface $dataHandler,
        HookDispatcherInterface $hookDispatcher,
        TranslatorInterface $translator,
        $isDemoModeEnabled,
        ExtraPropertiesFormDataPersister $extraPropertiesFormDataPersister
    ) {
        $this->dataHandler = $dataHandler;
        $this->hookDispatcher = $hookDispatcher;
        $this->translator = $translator;
        $this->isDemoModeEnabled = $isDemoModeEnabled;
        $this->extraPropertiesFormDataPersister = $extraPropertiesFormDataPersister;
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

        $entityId = $this->resolveExtraPropertyEntityId($newId ?? $id);
        if (null !== $entityId) {
            $this->extraPropertiesFormDataPersister->persist(
                $form,
                $this->getExtraPropertyEntityName($form),
                $entityId
            );
        }

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

        $entityId = $this->resolveExtraPropertyEntityId($id);
        if (null !== $entityId) {
            $this->extraPropertiesFormDataPersister->persist(
                $form,
                $this->getExtraPropertyEntityName($form),
                $entityId
            );
        }

        $this->hookDispatcher->dispatchWithParameters('actionAfterCreate' . Container::camelize($form->getName()) . 'FormHandler', [
            'id' => $id,
            'form_data' => &$data,
        ]);

        return FormHandlerResult::createWithId($id);
    }

    /**
     * Same entity key as FormBuilder (registry type block prefix). Do not use $form->getName() here:
     * the DOM/form tree name can differ from the type prefix (empty name, wrapper, createNamedBuilder).
     */
    private function getExtraPropertyEntityName(FormInterface $form): string
    {
        return $form->getConfig()->getType()->getBlockPrefix();
    }

    /**
     * Best-effort extraction of an integer entity id from a data handler result.
     *
     * Most handlers return a plain int, the rest an identity value object exposing
     * getValue(): int. Anything else (e.g. an array of ids) cannot be reduced to a
     * single int, so null is returned and the extra properties are simply not
     * persisted rather than crashing on a force cast.
     *
     * CreatedApiClient is handled as an explicit exception: it carries both an id
     * and a secret (so it has no top-level getValue()), but the id is needed to
     * persist extra properties. Such composite results are rare enough that an
     * ad hoc case is preferable to a generic, over-engineered resolution.
     *
     * @param mixed $id
     */
    private function resolveExtraPropertyEntityId($id): ?int
    {
        if (is_int($id) || (is_string($id) && ctype_digit($id))) {
            return (int) $id;
        }

        if ($id instanceof CreatedApiClient) {
            return $id->getApiClientId()->getValue();
        }

        if (is_object($id) && method_exists($id, 'getValue') && is_int($id->getValue())) {
            return $id->getValue();
        }

        return null;
    }
}
