<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Extension;

use PrestaShopBundle\Form\DataTransformer\DefaultEmptyDataTransformer;
use stdClass;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DefaultEmptyDataExtension extends every form type with additional default_empty_data option.
 */
class DefaultEmptyDataExtension extends AbstractTypeExtension
{
    /**
     * @var stdClass
     */
    private $privateEmptyValue;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        // We cannot use null as empty value so we create a private object that will allow us to detect that the
        // empty_view_data option has been explicitly set EVEN if it is null
        $this->privateEmptyValue = new stdClass();

        $resolver
            ->setDefaults([
                'default_empty_data' => null,
                'empty_view_data' => $this->privateEmptyValue,
            ])
            ->setAllowedTypes('default_empty_data', ['null', 'string', 'int', 'array', 'object', 'bool', 'float'])
            ->setAllowedTypes('empty_view_data', ['null', 'string', 'int', 'array', 'object', 'bool', 'float'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        if (!isset($options['default_empty_data'])) {
            return;
        }

        if ($options['empty_view_data'] !== $this->privateEmptyValue) {
            // This different use case is important because DefaultEmptyDataTransformer has a different behaviour when
            // the second parameter is provided, especially when you need to force null as the view data
            $transformer = new DefaultEmptyDataTransformer($options['default_empty_data'], $options['empty_view_data']);
        } else {
            $transformer = new DefaultEmptyDataTransformer($options['default_empty_data']);
        }

        $builder->addModelTransformer($transformer);
        $builder->addViewTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
