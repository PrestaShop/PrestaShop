<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Extension;

use PrestaShop\PrestaShop\Core\Localization\Number\LocaleNumberTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class IntegerTypeExtension extends AbstractTypeExtension
{
    /**
     * @var LocaleNumberTransformer
     */
    private $localeNumberTransformer;

    public function __construct(
        LocaleNumberTransformer $localeNumberTransformer
    ) {
        $this->localeNumberTransformer = $localeNumberTransformer;
    }

    public static function getExtendedTypes(): iterable
    {
        return [IntegerType::class];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // We only want to replace/adapt the IntegerToLocalizedStringTransformer, so we save the current transformers
        // to restore them after replacing the new IntegerToLocalizedStringTransformer.
        $viewTransformers = $builder->getViewTransformers();
        $builder->resetViewTransformers();

        foreach ($viewTransformers as $viewTransformer) {
            if ($viewTransformer instanceof IntegerToLocalizedStringTransformer) {
                $builder
                    ->addViewTransformer(new IntegerToLocalizedStringTransformer(
                        $options['grouping'],
                        $options['rounding_mode'],
                        !$options['grouping'] ? 'en' : $this->localeNumberTransformer->getLocaleForNumberInputs()
                    ))
                ;
            } else {
                $builder->addViewTransformer($viewTransformer);
            }
        }
    }
}
