<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadStatus;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerThreadStatusesChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getChoices(array $options = [])
    {
        return [
            $this->translator->trans('Opened', [], 'Admin.Catalog.Feature') => CustomerThreadStatus::OPEN,
            $this->translator->trans('Closed', [], 'Admin.Catalog.Feature') => CustomerThreadStatus::CLOSED,
            $this->translator->trans('Pending 1', [], 'Admin.Catalog.Feature') => CustomerThreadStatus::PENDING_1,
            $this->translator->trans('Pending 2', [], 'Admin.Catalog.Feature') => CustomerThreadStatus::PENDING_2,
        ];
    }
}
