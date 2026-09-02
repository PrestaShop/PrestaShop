<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\Permission;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides webservice key permissions choices
 */
final class PermissionsChoiceProvider implements FormChoiceProviderInterface
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
    public function getChoices()
    {
        return [
            $this->translator->trans('All', [], 'Admin.Global') => 'all',
            $this->translator->trans('View (GET)', [], 'Admin.Advparameters.Feature') => Permission::VIEW,
            $this->translator->trans('Modify (PUT)', [], 'Admin.Advparameters.Feature') => Permission::MODIFY,
            $this->translator->trans('Add (POST)', [], 'Admin.Advparameters.Feature') => Permission::ADD,
            $this->translator->trans('Patch (PATCH)', [], 'Admin.Advparameters.Feature') => Permission::PATCH,
            $this->translator->trans('Delete (DELETE)', [], 'Admin.Advparameters.Feature') => Permission::DELETE,
            $this->translator->trans('Fast view (HEAD)', [], 'Admin.Advparameters.Feature') => Permission::FAST_VIEW,
        ];
    }
}
