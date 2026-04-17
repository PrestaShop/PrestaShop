<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Theme;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AdaptToRTLLanguagesType is used as a form to select theme to adapt Right-to-Left languages.
 */
class AdaptThemeToRTLLanguagesType extends AbstractType
{
    /**
     * @var array
     */
    private $themeChoices;

    /**
     * @param string[] $themeChoices
     */
    public function __construct(array $themeChoices)
    {
        $this->themeChoices = $themeChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('theme_to_adapt', ChoiceType::class, [
                'choices' => $this->themeChoices,
            ])
            ->add('generate_rtl_css', SwitchType::class, [
                'required' => false,
                'data' => false,
            ])
        ;
    }
}
