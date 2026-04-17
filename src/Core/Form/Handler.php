<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form;

use Exception;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

/**
 * Complete implementation of FormHandlerInterface.
 */
class Handler implements FormHandlerInterface
{
    /**
     * @var string
     */
    public $form;

    /**
     * @var FormFactoryInterface the form factory
     */
    protected $formFactory;

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
     * @var string the form name
     */
    protected $formName;

    /**
     * FormHandler constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param HookDispatcherInterface $hookDispatcher
     * @param FormDataProviderInterface $formDataProvider
     * @param string $form
     * @param string $hookName
     * @param string $formName
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        HookDispatcherInterface $hookDispatcher,
        FormDataProviderInterface $formDataProvider,
        string $form,
        $hookName,
        $formName = 'form'
    ) {
        $this->formFactory = $formFactory;
        $this->hookDispatcher = $hookDispatcher;
        $this->formDataProvider = $formDataProvider;
        $this->form = $form;
        $this->hookName = $hookName;
        $this->formName = $formName;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function getForm()
    {
        $formBuilder = $this->formFactory->createNamedBuilder($this->formName, $this->form);

        $formBuilder->setData($this->formDataProvider->getData());

        $this->hookDispatcher->dispatchWithParameters(
            "action{$this->hookName}Form",
            [
                'form_builder' => $formBuilder,
            ]
        );

        return $formBuilder->getForm();
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
