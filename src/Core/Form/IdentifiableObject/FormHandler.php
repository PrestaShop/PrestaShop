<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FormHandler
 */
final class FormHandler implements FormHandlerInterface
{
    /**
     * @var string
     */
    private $formType;

    /**
     * @var FormDataProviderInterface
     */
    private $dataProvider;

    /**
     * @var FormDataHandlerInterface
     */
    private $dataHandler;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param string $formType
     * @param FormDataProviderInterface $dataProvider
     * @param FormDataHandlerInterface $dataHandler
     * @param FormFactoryInterface $formFactory
     * @param HookDispatcherInterface $hookDispatcher
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        $formType,
        FormDataProviderInterface $dataProvider,
        FormDataHandlerInterface $dataHandler,
        FormFactoryInterface $formFactory,
        HookDispatcherInterface $hookDispatcher,
        TranslatorInterface $translator,
        ConfigurationInterface $configuration
    ) {
        $this->formFactory = $formFactory;
        $this->dataProvider = $dataProvider;
        $this->dataHandler = $dataHandler;
        $this->hookDispatcher = $hookDispatcher;
        $this->translator = $translator;
        $this->configuration = $configuration;
        $this->formType = $formType;
    }

    /**
     * Get form for object creation with default data.
     *
     * @param array $options
     *
     * @return FormInterface
     */
    public function getForm(array $options = [])
    {
        return $this->buildForm(
            $this->dataProvider->getDefaultData(),
            null,
            $options
        );
    }

    /**
     * Get form for given object with pre-filled data.
     *
     * @param mixed $id
     * @param array $options
     *
     * @return FormInterface
     */
    public function getFormFor($id, array $options = [])
    {
        return $this->buildForm(
            $this->dataProvider->getData($id),
            $id,
            $options
        );
    }

    /**
     * Handle create form.
     *
     * @param FormInterface $form
     *
     * @return int ID of created object
     */
    public function handle(FormInterface $form)
    {
        return $this->handleForm($form);
    }

    /**
     * Update existing object with given form data.
     *
     * @param int $id
     * @param FormInterface $form
     *
     * @return int ID of updated object
     */
    public function handleFor($id, FormInterface $form)
    {
        return $this->handleForm($form, $id);
    }

    /**
     * @param array $data
     * @param int|null $id
     * @param array $options
     *
     * @return FormInterface
     */
    private function buildForm(array $data, $id = null, array $options = [])
    {
        $formBuilder = $this->formFactory->createBuilder($this->formType, $data, $options);

        $this->hookDispatcher->dispatchWithParameters('action' . $formBuilder->getName() . 'FormBuilderModifier', [
            'form_builder' => $formBuilder,
            'data' => &$data,
            'id' => $id,
        ]);

        return $formBuilder->getForm();
    }

    /**
     * @param FormInterface $form
     * @param int|null $id
     */
    private function handleForm(FormInterface $form, $id = null)
    {
        if (!$form->isSubmitted()) {
            return;
        }

        if ($this->configuration->get('_PS_MODE_DEMO_')) {
            $form->addError(
                new FormError(
                    $this->translator->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error')
                )
            );

            return;
        }

        if (!$form->isValid()) {
            return;
        }

        // form is submitted, demo mode is off, data is valid
        // we can dispatch hooks
        // persist data
        // return id
    }
}
