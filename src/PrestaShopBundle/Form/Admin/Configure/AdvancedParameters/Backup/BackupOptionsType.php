<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
