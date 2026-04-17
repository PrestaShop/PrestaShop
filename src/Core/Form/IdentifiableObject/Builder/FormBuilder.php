<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
