<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Import;

use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ImportFormHandler defines a form handler of import forms.
 */
class ImportFormHandler implements ImportFormHandlerInterface
{
    /**
     * Form builder.
     *
     * @var FormBuilderInterface
     */
    private $formBuilder;

    /**
     * Hook dispatcher.
     *
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var ImportFormDataProviderInterface
     */
    private $formDataProvider;
    /**
     * @var string
     */
    private $hookName;

    /**
     * @param FormBuilderInterface $formBuilder
     * @param HookDispatcherInterface $hookDispatcher
     * @param ImportFormDataProviderInterface $formDataProvider
     * @param string $hookName
     */
    public function __construct(
        FormBuilderInterface $formBuilder,
        HookDispatcherInterface $hookDispatcher,
        ImportFormDataProviderInterface $formDataProvider,
        $hookName
    ) {
        $this->formBuilder = $formBuilder;
        $this->hookDispatcher = $hookDispatcher;
        $this->formDataProvider = $formDataProvider;
        $this->hookName = $hookName;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(ImportConfigInterface $importConfig)
    {
        $this->formBuilder->setData($this->formDataProvider->getData($importConfig));
        $this->hookDispatcher->dispatchWithParameters(
            "action{$this->hookName}Form",
            [
                'form_builder' => $this->formBuilder,
            ]
        );

        return $this->formBuilder->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data)
    {
        $errors = $this->formDataProvider->setData($data);

        $this->hookDispatcher->dispatchWithParameters(
            "action{$this->hookName}Save",
            [
                'errors' => &$errors,
                'form_data' => $data,
            ]
        );

        return $errors;
    }
}
