<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Translations;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;

final class TranslationsSettingsFormHandler implements FormHandlerInterface
{
    /**
     * @var FormFactoryInterface the form builder
     */
    protected $formFactory;

    /**
     * @var HookDispatcherInterface the event dispatcher
     */
    protected $hookDispatcher;

    /**
     * @var string the hook name to be dispatched
     */
    protected $hookName;

    /**
     * @var string
     */
    protected $form;

    /**
     * @param FormFactoryInterface $formFactory
     * @param HookDispatcherInterface $hookDispatcher
     * @param string $form
     * @param string $hookName
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        HookDispatcherInterface $hookDispatcher,
        string $form,
        string $hookName
    ) {
        $this->formFactory = $formFactory;
        $this->hookDispatcher = $hookDispatcher;
        $this->form = $form;
        $this->hookName = $hookName;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        $formBuilder = $this->formFactory->createNamedBuilder('form', $this->form);

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
     */
    public function save(array $data)
    {
        // Translations forms do not save data
        return [];
    }
}
