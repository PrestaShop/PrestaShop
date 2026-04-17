<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\RequestSql;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class RequestSqlType defines RequestSql entity form type.
 */
class SqlRequestType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->trans('SQL query name', 'Admin.Advparameters.Feature'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [
                                sprintf('"%s"', $this->trans('SQL query name', 'Admin.Advparameters.Feature')),
                            ]
                        ),
                    ]),
                ],
            ])
            ->add('sql', TextareaType::class, [
                'label' => $this->trans('SQL query', 'Admin.Advparameters.Feature'),
                'attr' => [
                    'rows' => 10,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [
                                sprintf('"%s"', $this->trans('SQL query', 'Admin.Advparameters.Feature')),
                            ]
                        ),
                    ]),
                ],
            ]);
    }
}
