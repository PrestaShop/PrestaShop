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

namespace PrestaShopBundle\Form\Admin\Extension;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MultistoreConfigurationTypeExtension extends AbstractTypeExtension
{
    /**
     * @var FeatureInterface
     */
    private $multistoreFeature;

    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multistoreContext;

    /**
     * @var ShopConfigurationInterface
     */
    private $configuration;

    public function __construct(
        FeatureInterface $multistoreFeature,
        MultistoreContextCheckerInterface $multistoreContext,
        ShopConfigurationInterface $configuration
    ) {
        $this->multistoreFeature = $multistoreFeature;
        $this->multistoreContext = $multistoreContext;
        $this->configuration = $configuration;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$this->multistoreFeature->isUsed() || $this->multistoreContext->isAllShopContext()) {
            return;
        }

        $configuration = $this->configuration;
        $multistoreContext = $this->multistoreContext;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($configuration, $multistoreContext) {
            $form = $event->getForm();
            foreach ($form->all() as $child) {
                $options = $child->getConfig()->getOptions();
                if (!isset($options['attr']['multistore_configuration_key'])) {
                    continue;
                }

                // Check if current configuration is overidden by current shop / group shop context
                $shopConstraint = new ShopConstraint(
                    $multistoreContext->getContextShopId(),
                    $multistoreContext->getContextShopGroup()->id,
                    true
                );
                $isOveriddenInCurrentContext = $configuration->has($options['attr']['multistore_configuration_key'], $shopConstraint);

                // update current field with disabled attribute
                $options['attr']['disabled'] = !$multistoreContext->isAllShopContext() && !$isOveriddenInCurrentContext;
                $form->add(
                    $child->getName(),
                    get_class($child->getConfig()->getType()->getInnerType()),
                    $options
                );

                // for each field in the configuration form, we add a multistore checkbox
                $fieldName = 'multistore_' . $child->getName();
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
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return MultistoreConfigurationType::class;
    }
}
