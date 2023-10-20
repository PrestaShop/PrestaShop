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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Service\Form;

use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Controller\Admin\MultistoreController;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;

/**
 * Class MultistoreCheckboxEnabler
 *
 * This class is responsible for enabling multistore checkboxes on BO configuration forms,
 * it is usually called from the MultistoreConfigurationTypeExtension.
 *
 * Checkboxes are added or not, and checked or not depending on the current multistore context,
 * and if the field has the required attribute `multistore_configuration_key`.
 *
 * @todo add a link to the documentation related to this part, when it's online
 *
 * @see MaintenanceType for an example of a form that is configured to enable multistore checkboxes on its fields
 * @see MaintenanceConfiguration for an example of how to extend and use the AbstractMultistoreConfiguration to store multistore configuration values
 * @see MultistoreConfigurationTypeExtension this is the form extension that calls this class
 * @see AbstractMultistoreConfiguration this is the abstraction used by the class responsible for storing multistore configuration values
 */
class MultistoreCheckboxEnabler
{
    public const MULTISTORE_FIELD_PREFIX = 'multistore_';

    /**
     * @var FeatureInterface
     */
    private $multistoreFeature;

    /**
     * @var ShopConfigurationInterface
     */
    private $configuration;

    /**
     * @var Context
     */
    private $multiStoreContext;

    /**
     * @var MultistoreController
     */
    private $multistoreController;

    /**
     * @var FormCloner
     */
    private $formCloner;

    /**
     * MultistoreCheckboxEnabler constructor.
     *
     * @param FeatureInterface $multistoreFeature
     * @param ShopConfigurationInterface $configuration
     * @param Context $multiStoreContext
     * @param MultistoreController $multistoreController
     * @param FormCloner $formCloner
     */
    public function __construct(
        FeatureInterface $multistoreFeature,
        ShopConfigurationInterface $configuration,
        Context $multiStoreContext,
        MultistoreController $multistoreController,
        FormCloner $formCloner
    ) {
        $this->multistoreFeature = $multistoreFeature;
        $this->configuration = $configuration;
        $this->multiStoreContext = $multiStoreContext;
        $this->multistoreController = $multistoreController;
        $this->formCloner = $formCloner;
    }

    /**
     * @return bool
     */
    public function shouldAddMultistoreElements(): bool
    {
        if (!$this->multistoreFeature->isUsed()) {
            return false;
        }

        return true;
    }

    /**
     * Adds multistore checkboxes to form fields if needed,
     *
     * @param FormInterface $form (passed by reference)
     */
    public function addMultistoreElements(FormInterface $form): void
    {
        foreach ($form->all() as $child) {
            $options = $child->getConfig()->getOptions();
            if (!isset($options['multistore_configuration_key'])) {
                continue;
            }

            $isOverriddenInCurrentContext = $this->isOverriddenInCurrentContext($options['multistore_configuration_key']);

            // update current field with disabled attribute
            $this->updateCurrentField($form, $child, $options, $isOverriddenInCurrentContext);

            // for each field in the configuration form, we add a multistore checkbox (except in all shop context)
            if (!$this->multiStoreContext->isAllShopContext()) {
                $this->addCheckbox($form, $child->getName(), $isOverriddenInCurrentContext, $options['multistore_configuration_key']);
            }
        }
    }

    /**
     * Check if given configuration value is overridden by current shop / group shop context
     *
     * @param string $configurationKey
     *
     * @return bool
     */
    private function isOverriddenInCurrentContext(string $configurationKey): bool
    {
        // Check if current configuration is overridden by current shop / group shop context
        // The $isStrict parameter is important: it will return a value only if it's present, skipping the hierarchical fallback system
        return $this->configuration->has($configurationKey, $this->multiStoreContext->getShopConstraint(true));
    }

    /**
     * Update current field with `disabled` attribute value and multistore dropdown
     *
     * @param FormInterface $form
     * @param FormInterface $childElement
     * @param array $options
     * @param bool $isOverriddenInCurrentContext
     */
    private function updateCurrentField(FormInterface $form, FormInterface $childElement, array &$options, bool $isOverriddenInCurrentContext): void
    {
        $options['attr']['disabled'] = !$this->multiStoreContext->isAllShopContext() && !$isOverriddenInCurrentContext;

        // add multistore dropdown in field option
        if ($this->multiStoreContext->isAllShopContext() || $this->multiStoreContext->isGroupShopContext()) {
            $options['multistore_dropdown'] = $this->multistoreController->configurationDropdown(
                $this->configuration,
                $options['multistore_configuration_key']
            )->getContent();
        }

        // clone the field so that we keep all existing options, model transformers, listeners, etc...
        $clonedField = $this->formCloner->cloneForm($form->get($childElement->getName()), $options);

        $form->add($clonedField);
    }

    /**
     * Add multistore checkbox to given related field
     *
     * @param FormInterface $form
     * @param string $relatedFieldName
     * @param bool $isOverriddenInCurrentContext
     * @param string $configurationKey
     */
    private function addCheckbox(FormInterface $form, string $relatedFieldName, bool $isOverriddenInCurrentContext, string $configurationKey): void
    {
        $fieldName = self::MULTISTORE_FIELD_PREFIX . $relatedFieldName;
        $form->add($fieldName, CheckboxType::class, [
            'required' => false,
            'data' => $isOverriddenInCurrentContext,
            'multistore_configuration_key' => $configurationKey,
            'label' => false,
            'attr' => [
                'material_design' => true,
                'class' => 'multistore-checkbox',
            ],
        ]);
    }
}
