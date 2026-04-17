<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Extension;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Service\Form\MultistoreCheckboxEnabler;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;

class MultistoreConfigurationTypeExtension extends AbstractTypeExtension
{
    /**
     * @var MultistoreCheckboxEnabler
     */
    private $multistoreCheckboxEnabler;

    public function __construct(MultistoreCheckboxEnabler $multistoreCheckboxEnabler)
    {
        $this->multistoreCheckboxEnabler = $multistoreCheckboxEnabler;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$this->multistoreCheckboxEnabler->shouldAddMultistoreElements()) {
            return;
        }

        // when we have multistore checkboxes, we send partial data, so we need to use the http PATCH method
        $builder->setMethod(Request::METHOD_PATCH);

        $checkboxEnabler = $this->multistoreCheckboxEnabler;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($checkboxEnabler) {
            $form = $event->getForm();
            $checkboxEnabler->addMultistoreElements($form);
        });
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [MultistoreConfigurationType::class];
    }
}
