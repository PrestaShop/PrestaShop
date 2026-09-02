<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Entity\Repository\TimezoneRepository;

/**
 * Class TimezoneByNameChoiceProvider provides timezone choices with name values.
 */
final class TimezoneByNameChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TimezoneRepository
     */
    private $timezoneRepository;

    /**
     * @param TimezoneRepository $timezoneRepository
     */
    public function __construct(TimezoneRepository $timezoneRepository)
    {
        $this->timezoneRepository = $timezoneRepository;
    }

    /**
     * Get timezone choices.
     *
     * @return array
     */
    public function getChoices()
    {
        return FormChoiceFormatter::formatFormChoices(
            $this->timezoneRepository->findAll(),
            'name',
            'name'
        );
    }
}
