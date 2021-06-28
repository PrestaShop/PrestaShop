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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Backup;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class BackupOptionsType builds form for backup options.
 */
class BackupOptionsType extends TranslatorAwareType
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * BackupOptionsType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param Configuration $configuration
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        Configuration $configuration
    ) {
        parent::__construct($translator, $locales);
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $backupAllHelp = $this->trans(
            'Drop existing tables during import.',
            'Admin.Advparameters.Help'
        );

        $backupAllHelp .= '<br>';
        $backupAllHelp .= str_replace(
            '%prefix%',
            $this->configuration->get('_DB_PREFIX_'),
            '%prefix%connections, %prefix%connections_page %prefix%connections_source, %prefix%guest, %prefix%statssearch'
        );

        $backupDropTablesHelp = $this->trans(
            'If enabled, the backup script will drop your tables prior to restoring data. (ie. "DROP TABLE IF EXISTS")',
            'Admin.Advparameters.Help'
        );

        $builder
            ->add('backup_all', SwitchType::class, [
                'label' => $this->trans('Ignore statistics tables', 'Admin.Advparameters.Feature'),
                'help' => $backupAllHelp,
            ])
            ->add('backup_drop_tables', SwitchType::class, [
                'label' => $this->trans('Drop existing tables during import', 'Admin.Advparameters.Feature'),
                'help' => $backupDropTablesHelp,
            ]);
    }
}
