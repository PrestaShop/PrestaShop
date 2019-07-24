<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

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
     * @param FormFactoryInterface $formFactory
     * @param HookDispatcherInterface $hookDispatcher
     * @param FormDataProviderInterface $dataProvider
     * @param string $formType
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        HookDispatcherInterface $hookDispatcher,
        FormDataProviderInterface $dataProvider,
        $formType
    ) {
        $this->formFactory = $formFactory;
        $this->hookDispatcher = $hookDispatcher;
        $this->dataProvider = $dataProvider;
        $this->formType = $formType;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(array $data = [], array $options = [])
    {
        if (is_array($defaultData = $this->dataProvider->getDefaultData())) {
            $data = array_merge($defaultData, $data);
        }

        return $this->buildForm(
            $this->formType,
            $data,
            null,
            $options
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFormFor($id, array $data = [], array $options = [])
    {
        $data = array_merge($this->dataProvider->getData($id), $data);

        return $this->buildForm(
            $this->formType,
            $data,
            $id,
            $options
        );
    }

    /**
     * @param string $formType
     * @param array $data
     * @param int|null $id
     * @param array $options
     *
     * @return FormInterface
     */
    private function buildForm($formType, $data, $id = null, array $options = [])
    {
        $formBuilder = $this->formFactory->createBuilder($formType, $data, $options);

        $this->hookDispatcher->dispatchWithParameters('action' . Container::camelize($formBuilder->getName()) . 'FormBuilderModifier', [
            'form_builder' => $formBuilder,
            'data' => &$data,
            'id' => $id,
        ]);

        return $formBuilder->getForm();
    }
}
