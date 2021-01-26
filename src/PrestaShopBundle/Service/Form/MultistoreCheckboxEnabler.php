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

namespace PrestaShopBundle\Service\Form;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;
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
 * @see MaintenanceConfiguration for an example of how to extend and use the MultistoreConfigurator to store multistore configuration values
 * @see MultistoreConfigurationTypeExtension this is the form extension that calls this class
 * @see MultistoreConfigurator this is the abstract used by the class responsible for storing multistore configuration values
 */
class MultistoreCheckboxEnabler
{
    private $multistoreFeature;
    private $configuration;
    private $context;

    public const MULTISTORE_FIELD_PREFIX = 'multistore_';

    /**
     * MultistoreCheckboxEnabler constructor.
     *
     * @param FeatureInterface $multistoreFeature
     * @param ShopConfigurationInterface $configuration
     * @param MultistoreContextCheckerInterface $context
     */
    public function __construct(
        FeatureInterface $multistoreFeature,
        ShopConfigurationInterface $configuration,
        MultistoreContextCheckerInterface $context
    ) {
        $this->multistoreFeature = $multistoreFeature;
        $this->configuration = $configuration;
        $this->context = $context;
    }

    /**
     * @return bool
     */
    public function shouldAddCheckboxes(): bool
    {
        if (!$this->multistoreFeature->isUsed() || $this->context->isAllShopContext()) {
            return false;
        }

        return true;
    }

    /**
     * Adds multistore checkboxes to form fields if needed,
     *
     * @param FormInterface $form (passed by reference)
     *
     * @throws \PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException
     */
    public function addCheckboxes(FormInterface &$form): void
    {
        foreach ($form->all() as $child) {
            $options = $child->getConfig()->getOptions();
            if (!isset($options['attr']['multistore_configuration_key'])) {
                continue;
            }

            // Check if current configuration is overridden by current shop / group shop context
            $shopConstraint = new ShopConstraint(
                $this->context->getContextShopId(),
                $this->context->getContextShopGroup()->id,
                true
            );
            $isOveriddenInCurrentContext = $this->configuration->has($options['attr']['multistore_configuration_key'], $shopConstraint);

            // update current field with disabled attribute
            $options['attr']['disabled'] = !$this->context->isAllShopContext() && !$isOveriddenInCurrentContext;
            $form->add(
                $child->getName(),
                get_class($child->getConfig()->getType()->getInnerType()),
                $options
            );

            // for each field in the configuration form, we add a multistore checkbox
            $fieldName = self::MULTISTORE_FIELD_PREFIX . $child->getName();
            $form->add($fieldName, CheckboxType::class, [
                'required' => false,
                'data' => $isOveriddenInCurrentContext,
                'label' => false,
                'attr' => [
                    'material_design' => true,
                    'class' => 'multistore-checkbox',
                    'multistore_configuration_key' => $options['attr']['multistore_configuration_key'],
                ],
            ]);
        }
    }
}
