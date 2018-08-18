<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Backup\Modifier;

use PrestaShop\PrestaShop\Core\Grid\Modifier\ModifierInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\TimeDefinition;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class HumanReadableBackupAgeModifier modifies age to be human readable
 */
final class HumanReadableBackupAgeModifier implements ModifierInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function modify(array $record)
    {
        $record['age'] = $this->getHumanReadableAge($record['age']);

        return $record;
    }

    /**
     * Get age that is human readable
     *
     * @param int $ageInSeconds
     *
     * @return string
     */
    private function getHumanReadableAge($ageInSeconds)
    {
        if (TimeDefinition::HOUR_IN_SECONDS > $ageInSeconds) {
            return sprintf('< 1 %s', $this->translator->trans('Hour', [], 'Admin.Global'));
        }

        if (TimeDefinition::DAY_IN_SECONDS > $ageInSeconds) {
            $hours = (int) floor($ageInSeconds / TimeDefinition::HOUR_IN_SECONDS);
            $label = 1 === $hours ?
                $this->translator->trans('Hour', [], 'Admin.Global') :
                $this->translator->trans('Hours', [], 'Admin.Global');

            return sprintf('%s %s', $hours, $label);
        }

        $days = (int) floor($ageInSeconds / TimeDefinition::DAY_IN_SECONDS);
        $label = 1 === $days ?
            $this->translator->trans('Day', [], 'Admin.Global') :
            $this->translator->trans('Days', [], 'Admin.Global');

        return sprintf('%s %s', $days, $label);
    }
}
