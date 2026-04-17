<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\EventListener;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * This listener dynamically updates the choices allowed in the specific price combination id selector,
 * because all the choices are populated using javascript
 */
class SpecificPriceCombinationListener implements EventSubscriberInterface
{
    /**
     * @var FormCloner
     */
    private $formCloner;

    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    public function __construct(
        FormCloner $formCloner,
        CombinationRepository $combinationRepository
    ) {
        $this->formCloner = $formCloner;
        $this->combinationRepository = $combinationRepository;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'updateCombinationChoices',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function updateCombinationChoices(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (!isset($data['combination_id'])) {
            return;
        }

        $combinationId = (int) $data['combination_id'];
        $choices = [NoCombinationId::NO_COMBINATION_ID];

        if ($combinationId !== NoCombinationId::NO_COMBINATION_ID) {
            $this->combinationRepository->assertCombinationExists(new CombinationId($combinationId));
            $choices[] = $combinationId;
        }

        // add new choices, so it throw raise invalid choice error
        $newCombinationChoicesForm = $this->formCloner->cloneForm($form->get('combination_id'), [
            'choices' => $choices,
        ]);

        $form->add($newCombinationChoicesForm);
    }
}
