<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class is responsible to create a date picker field.
 */
class DatePickerType extends AbstractType
{
    /**
     * @var DataTransformerInterface
     */
    private $arabicToLatinDigitDataTransformer;

    public function __construct(DataTransformerInterface $arabicToLatinDigitDataTransformer)
    {
        $this->arabicToLatinDigitDataTransformer = $arabicToLatinDigitDataTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer($this->arabicToLatinDigitDataTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['date_format'] = $options['date_format'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'widget' => 'single_text',
            // date_format must be provided in javascript supported type
            'date_format' => 'YYYY-MM-DD',
        ]);
        $resolver->setAllowedTypes('date_format', 'string');
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'date_picker';
    }
}
