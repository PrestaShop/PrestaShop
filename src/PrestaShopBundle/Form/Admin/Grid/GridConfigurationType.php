<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Grid;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class GridConfigurationType extends TranslatorAwareType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('display_shared_filters', CheckboxType::class, [
                'label' => $this->trans('Display shared filters', 'Admin.Global'),
                'required' => false,
            ])
            ->add('display_totals', CheckboxType::class, [
                'label' => $this->trans('Display record counts', 'Admin.Global'),
                'required' => false,
            ])
            ->add('controller_route', HiddenType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255]),
                    new Regex(['pattern' => '/^[a-zA-Z0-9_.]+$/']),
                ],
            ])
            ->add('filter_id', HiddenType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 191]),
                    new Regex(['pattern' => '/^[a-zA-Z0-9_-]+$/']),
                ],
            ])
        ;
    }
}
