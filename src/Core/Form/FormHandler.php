<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form;

use Exception;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

/**
 * Complete implementation of FormHandlerInterface.
 *
 * @deprecated since 1.7.8, will be removed in the next major version, use Handler.php instead
 */
class FormHandler implements FormHandlerInterface
{
    /**
     * @var FormBuilderInterface the form builder
     */
    protected $formBuilder;

    /**
     * @var FormDataProviderInterface the form data provider
     */
    protected $formDataProvider;

    /**
     * @var HookDispatcherInterface the event dispatcher
     */
    protected $hookDispatcher;

    /**
     * @var string the hook name
     */
    protected $hookName;

    /**
     * @var array the list of Form Types
     */
    protected $formTypes;

    /**
     * @var string the form name
     */
    protected $formName;

    /**
     * FormHandler constructor.
     *
     * @param FormBuilderInterface $formBuilder
     * @param HookDispatcherInterface $hookDispatcher
     * @param FormDataProviderInterface $formDataProvider
     * @param array $formTypes
     * @param string $hookName
     * @param string $formName
     */
    public function __construct(
        FormBuilderInterface $formBuilder,
        HookDispatcherInterface $hookDispatcher,
        FormDataProviderInterface $formDataProvider,
        array $formTypes,
        $hookName,
        $formName = 'form'
    ) {
        $this->formName = $formName;
        $this->formBuilder = $formBuilder->getFormFactory()->createNamedBuilder($formName);
        $this->hookDispatcher = $hookDispatcher;
        $this->formDataProvider = $formDataProvider;
        $this->formTypes = $formTypes;
        $this->hookName = $hookName;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getForm()
    {
        foreach ($this->formTypes as $formName => $formType) {
            $this->formBuilder->add($formName, $formType);
        }

        $this->formBuilder->setData($this->formDataProvider->getData());
        $this->hookDispatcher->dispatchWithParameters(
            "action{$this->hookName}Form",
            [
                'form_builder' => &$this->formBuilder,
            ]
        );

        return $this->formBuilder->getForm();
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     * @throws UndefinedOptionsException
     */
    public function save(array $data)
    {
        $errors = $this->formDataProvider->setData($data);

        $this->hookDispatcher->dispatchWithParameters(
            "action{$this->hookName}Save",
            [
                'errors' => &$errors,
                'form_data' => &$data,
            ]
        );

        return $errors;
    }
}
