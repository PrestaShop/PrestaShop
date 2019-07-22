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

namespace PrestaShopBundle\Form\Admin\Extension;

use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;
use PrestaShopBundle\Form\Admin\Type\ConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ConfigurationTypeExtension extends AbstractTypeExtension
{
    /**
     * @var FeatureInterface
     */
    private $multistoreFeature;

    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multistoreContext;

    public function __construct(
        FeatureInterface $multistoreFeature,
        MultistoreContextCheckerInterface $multistoreContext
    ) {
        $this->multistoreFeature = $multistoreFeature;
        $this->multistoreContext = $multistoreContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $addMultistoreConfig = $this->multistoreFeature->isUsed() && !$this->multistoreContext->isAllShopContext();

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($addMultistoreConfig) {
            if (!$addMultistoreConfig) {
                return;
            }

            $form = $event->getForm();

            $configFields = [];

            foreach ($form->all() as $child) {
                $configFields[$child->getName()] = $child->getName();
            }

            $form->add('multistore_config_switch', SwitchType::class, [
                'required' => false,
                'data' => false,
            ]);

            $form->add('multistore_config', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'choices' => $configFields,
            ]);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ConfigurationType::class;
    }
}
