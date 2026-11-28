<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Contact\Repository\ContactRepository;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class ContactByIdChoiceProvider is responsible for providing shop contact choices.
 */
final class ContactByIdChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var ContactRepository
     */
    private $contactRepository;

    /**
     * @var int
     */
    private $langId;

    /**
     * @param ContactRepository $contactRepository
     * @param int $langId
     */
    public function __construct(
        ContactRepository $contactRepository,
        $langId
    ) {
        $this->contactRepository = $contactRepository;
        $this->langId = $langId;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return FormChoiceFormatter::formatFormChoices(
            $this->contactRepository->findAllByLangId($this->langId),
            'id_contact',
            'name'
        );
    }
}
