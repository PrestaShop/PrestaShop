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
     * @var array the list of Form Types
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
