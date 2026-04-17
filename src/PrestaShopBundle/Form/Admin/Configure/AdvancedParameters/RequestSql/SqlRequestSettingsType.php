<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\RequestSql;

use PrestaShop\PrestaShop\Core\Encoding\CharsetEncoding;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class RequestSqlSettingsType build form type for "Configure > Advanced Parameters > Database > SQL Manager" page.
 */
class SqlRequestSettingsType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('default_file_encoding', ChoiceType::class, [
                'label' => $this->trans('Select your default file encoding', 'Admin.Advparameters.Feature'),
                'choices' => [
                    CharsetEncoding::UTF_8 => CharsetEncoding::UTF_8,
                    CharsetEncoding::ISO_8859_1 => CharsetEncoding::ISO_8859_1,
                ],
                'translation_domain' => false,
            ])
            ->add('default_file_separator', TextType::class, [
                'label' => $this->trans('Select your default file separator', 'Admin.Advparameters.Feature'),
                'translation_domain' => false,
                'attr' => [
                    'maxlength' => '1',
                    'required',
                ],
            ])
            ->add('enable_multi_statements', SwitchType::class, [
                'label' => $this->trans('Enable multi-statements queries', 'Admin.Advparameters.Feature'),
                'help' => $this->trans(
                    'Enabling multi-statements queries increases the risk of SQL injection vulnerabilities being exploited.',
                    'Admin.Advparameters.Help'
                ),
            ]);
    }
}
