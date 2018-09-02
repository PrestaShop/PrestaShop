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

use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class EntityFormHandler
 */
final class EntityFormHandler implements EntityFormHandlerInterface
{
    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var string Fully qualified form type class name
     */
    private $entityFormType;

    /**
     * @var string
     */
    private $entityName;

    /**
     * @var EntityFormDataProviderInterface
     */
    private $entityFormDataProvider;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param FormFactoryInterface $formFactory
     * @param EntityFormDataProviderInterface $entityFormDataProvider
     * @param string $entityFormType
     * @param string $entityName
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        FormFactoryInterface $formFactory,
        EntityFormDataProviderInterface $entityFormDataProvider,
        $entityFormType,
        $entityName
    ) {
        $this->hookDispatcher = $hookDispatcher;
        $this->formFactory = $formFactory;
        $this->entityFormDataProvider = $entityFormDataProvider;
        $this->entityFormType = $entityFormType;
        $this->entityName = $entityName;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        return $this->getEntityForm();
    }

    /**
     * {@inheritdoc}
     */
    public function getFormFor($entityId)
    {
        $entityData = $this->entityFormDataProvider->getData($entityId);

        return $this->getEntityForm($entityData);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormWith(array $data)
    {
        return $this->getEntityForm($data);
    }

    /**
     * Get entity form with data
     *
     * @param array $formData Form data if any
     *
     * @return FormInterface
     */
    private function getEntityForm(array $formData = [])
    {
        $entityFormBuilder = $this->formFactory->createNamedBuilder(
            $this->entityName,
            $this->entityFormType,
            $formData
        );

        $this->hookDispatcher->dispatchWithParameters('action' . $this->entityName . 'FormBuilderModifier', [
            'form_builder' => $entityFormBuilder,
        ]);

        return $this->getForm();
    }
}
