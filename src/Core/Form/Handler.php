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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
