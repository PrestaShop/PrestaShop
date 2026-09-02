<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopLogger;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LogSeverityChoiceType.
 */
class LogSeverityChoiceType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->getSeveritysChoices(),
            'required' => false,
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

    private function getSeveritysChoices()
    {
        return [
            $this->trans('Debug', 'Admin.Advparameters.Help') => PrestaShopLogger::LOG_SEVERITY_LEVEL_DEBUG,
            $this->trans('Informative only', 'Admin.Advparameters.Help') => PrestaShopLogger::LOG_SEVERITY_LEVEL_INFORMATIVE,
            $this->trans('Warning', 'Admin.Advparameters.Help') => PrestaShopLogger::LOG_SEVERITY_LEVEL_WARNING,
            $this->trans('Error', 'Admin.Advparameters.Help') => PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR,
            $this->trans('Major issue (crash)!', 'Admin.Advparameters.Help') => PrestaShopLogger::LOG_SEVERITY_LEVEL_MAJOR,
        ];
    }
}
