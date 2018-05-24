<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Core\Form;

use Exception;
use PrestaShopBundle\Service\Hook\HookDispatcher;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolverException\UndefinedOptionsException;

/**
 * Complete implementation of FormHandlerInterface
 */
class FormHandler implements FormHandlerInterface
{
    /**
     * @var FormBuilderInterface the form builder.
     */
    protected $formBuilder;

    /**
     * @var FormDataProviderInterface the form data provider.
     */
    protected $formDataProvider;

    /**
     * @var HookDispatcher the event dispatcher.
     */
    protected $hookDispatcher;

    /**
     * @var string the hook name.
     */
    protected $hookName;

    /**
     * @var array the list of Form Types.
     */
    protected $formTypes;

    public function __construct(
        FormBuilderInterface $formBuilder,
        HookDispatcher $hookDispatcher,
        FormDataProviderInterface $formDataProvider,
        array $formTypes,
        $hookName
    ) {
        $this->formBuilder = $formBuilder;
        $this->hookDispatcher = $hookDispatcher;
        $this->formDataProvider = $formDataProvider;
        $this->formTypes = $formTypes;
        $this->hookName = $hookName;
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function getForm()
    {
        foreach ($this->formTypes as $formName => $formType) {
            $this->formBuilder->add($formName, $formType);
        }

        $this->formBuilder->setData($this->formDataProvider->getData());
        $this->hookDispatcher->dispatchForParameters(
            "action{$this->hookName}Form",
            [
                'form_builder' => &$this->formBuilder,
            ]
        );

        return $this->formBuilder->getForm();
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     * @throws UndefinedOptionsException
     */
    public function save(array $data)
    {
        $errors = $this->formDataProvider->setData($data);

        $this->hookDispatcher->dispatchForParameters(
            "action{$this->hookName}Save",
            [
                'errors' => &$errors,
                'form_data' => &$data,
            ]
        );

        return $errors;
    }
}
