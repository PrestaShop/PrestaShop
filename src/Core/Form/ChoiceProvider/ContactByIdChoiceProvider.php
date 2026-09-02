<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Support\ContactRepositoryInterface;

/**
 * Class ContactByIdChoiceProvider is responsible for providing shop contact choices.
 */
final class ContactByIdChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var ContactRepositoryInterface
     */
    private $contactRepository;

    /**
     * @var int
     */
    private $langId;

    /**
     * @param ContactRepositoryInterface $contactRepository
     * @param int $langId
     */
    public function __construct(
        ContactRepositoryInterface $contactRepository,
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
