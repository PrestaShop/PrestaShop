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

namespace PrestaShopBundle\Form\EventSubscriber;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Subscriber resolves dynamical customer address form fields options and validation
 */
class CustomerAddressFormSubscriber implements EventSubscriberInterface
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $stateChoiceProvider;

    /**
     * @param ConfigurableFormChoiceProviderInterface $stateChoiceProvider
     */
    public function __construct(ConfigurableFormChoiceProviderInterface $stateChoiceProvider)
    {
        $this->stateChoiceProvider = $stateChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $this->resolveStateField($event);
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $this->resolveStateField($event);
    }

    /**
     * @param FormEvent $event
     */
    private function resolveStateField(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $countryId = (int) $data['id_country'];
        $choices = array_merge(
            ['-' => ''],
            $this->stateChoiceProvider->getChoices(['id_country' => $countryId])
        );
        $options = [
            'required' => false,
            'disabled' => true,
            'placeholder' => false,
            'choices' => [],
        ];

        if (!empty($choices)) {
            $options = array_merge($options,
                [
                    'required' => false,
                    'disabled' => false,
                    'choices' => $choices,
                    'constraints' => [
                        new NotBlank(
                            [
                                'groups' => 'state',
                            ]
                        ),
                    ],
                ]
            );
        }

        $form->add('id_state', ChoiceType::class, $options);
    }
}
