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
