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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider\FormOptionsProviderInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRegistryInterface;

/**
 * Creates new forms for identifiable objects.
 */
final class FormBuilder implements FormBuilderInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var FormDataProviderInterface
     */
    private $dataProvider;

    /**
     * @var string
     */
    private $formType;

    /**
     * @var FormOptionsProviderInterface|null
     */
    private $optionsProvider;

    /**
     * @var FormRegistryInterface|null
     */
    private $registry;

    /**
     * @param FormFactoryInterface $formFactory
     * @param HookDispatcherInterface $hookDispatcher
     * @param FormDataProviderInterface $dataProvider
     * @param string $formType
     * @param FormRegistryInterface $registry
     * @param FormOptionsProviderInterface|null $optionsProvider
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        HookDispatcherInterface $hookDispatcher,
        FormDataProviderInterface $dataProvider,
        string $formType,
        FormRegistryInterface $registry,
        ?FormOptionsProviderInterface $optionsProvider = null
    ) {
        $this->formFactory = $formFactory;
        $this->hookDispatcher = $hookDispatcher;
        $this->dataProvider = $dataProvider;
        $this->formType = $formType;
        $this->registry = $registry;
        $this->optionsProvider = $optionsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(array $data = [], array $options = [])
    {
        // Fetch default data
        $defaultData = $this->dataProvider->getDefaultData();
        // Merge initial data in default data
        if (is_array($defaultData)) {
            $data = array_merge($defaultData, $data);
        }

        if (null !== $this->optionsProvider) {
            $options = array_merge($this->optionsProvider->getDefaultOptions($data), $options);
        }

        // Hook action<FormName>FormDataProviderDefaultData
        $this->hookDispatcher->dispatchWithParameters(
            'action' . $this->camelize($this->getFormName()) . 'FormDataProviderDefaultData',
            [
                'data' => &$data,
                'options' => &$options,
            ]
        );

        return $this->buildForm(
            $this->formType,
            $data,
            $options
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFormFor($id, array $data = [], array $options = [])
    {
        $data = array_merge($this->dataProvider->getData($id), $data);

        if (null !== $this->optionsProvider) {
            $options = array_merge($this->optionsProvider->getOptions($id, $data), $options);
        }

        // Hook action<FormName>FormDataProviderData
        $this->hookDispatcher->dispatchWithParameters(
            'action' . $this->camelize($this->getFormName()) . 'FormDataProviderData',
            [
                'data' => &$data,
                'id' => $id,
                'options' => &$options,
            ]
        );

        return $this->buildForm(
            $this->formType,
            $data,
            $options,
            $id
        );
    }

    /**
     * @param string $formType
     * @param array $data
     * @param array $options
     * @param int|null $id
     *
     * @return FormInterface
     */
    private function buildForm($formType, $data, array $options = [], $id = null)
    {
        $formBuilder = $this->formFactory->createBuilder($formType, $data, $options);

        $this->hookDispatcher->dispatchWithParameters('action' . $this->camelize($formBuilder->getName()) . 'FormBuilderModifier', [
            'form_builder' => $formBuilder,
            'data' => &$data,
            'options' => &$options,
            'id' => $id,
        ]);

        return $formBuilder->getForm();
    }

    /**
     * @param string $hookName
     *
     * @return string
     */
    private function camelize(string $hookName): string
    {
        return Container::camelize($hookName);
    }

    private function getFormName(): string
    {
        return $this->registry->getType($this->formType)->getBlockPrefix();
    }
}
