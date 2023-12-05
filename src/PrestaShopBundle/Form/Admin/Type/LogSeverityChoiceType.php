<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopLogger;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LogSeverityChoiceType.
 * 
 * @link https://devdocs.prestashop-project.org/8/development/components/form/types-reference/log-severity-choice-type/
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
            $this->trans('Informative only', 'Admin.Advparameters.Help') => PrestaShopLogger::LOG_SEVERITY_LEVEL_INFORMATIVE,
            $this->trans('Warning', 'Admin.Advparameters.Help') => PrestaShopLogger::LOG_SEVERITY_LEVEL_WARNING,
            $this->trans('Error', 'Admin.Advparameters.Help') => PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR,
            $this->trans('Major issue (crash)!', 'Admin.Advparameters.Help') => PrestaShopLogger::LOG_SEVERITY_LEVEL_MAJOR,
        ];
    }
}
