<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class YesAndNoChoiceType.
 */
class YesAndNoChoiceType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                $this->trans('Yes', 'Admin.Global') => 1,
                $this->trans('No', 'Admin.Global') => 0,
            ],
            'required' => false,
            'placeholder' => $this->trans('All', 'Admin.Global'),
            'choice_translation_domain' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
