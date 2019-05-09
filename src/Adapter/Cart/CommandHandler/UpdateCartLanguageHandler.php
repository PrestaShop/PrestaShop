<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Cart;
use Language;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateCartLanguageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

/**
 * @internal
 */
final class UpdateCartLanguageHandler extends AbstractCartHandler implements UpdateCartLanguageHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCartLanguageCommand $command)
    {
        $language = $this->getLanguageObject($command->getNewLanguageId());

        $this->assertLanguageIsActive($language);

        $cart = $this->getCartObject($command->getCartId());
        $cart->id_lang = (int) $language->id;

        if (false === $cart->save()) {
            throw new CartException('Failed to update cart');
        }

        // @todo: Should context be changed at controller layer instead?
        \Context::getContext()->cart = $cart;
    }

    /**
     * @param LanguageId $languageId
     *
     * @throws LanguageNotFoundException
     */
    private function getLanguageObject(LanguageId $languageId)
    {
        $lang = new Language($languageId->getValue());

        if ($languageId->getValue() !== $lang->id) {
            throw new LanguageNotFoundException(
                $languageId,
                sprintf('Language with id "%s" was not found', $languageId->getValue())
            );
        }
    }

    /**
     * @param Language $lang
     */
    private function assertLanguageIsActive(Language $lang)
    {
        if ($lang->active) {
            throw new LanguageException('Language with id "%s" is not active ');
        }
    }
}
