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

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Language;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateCartLanguageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShopException;

/**
 * @internal
 */
final class UpdateCartLanguageHandler extends AbstractCartHandler implements UpdateCartLanguageHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCartLanguageCommand $command): void
    {
        $language = $this->getLanguageObject($command->getNewLanguageId());

        $this->assertLanguageIsActive($language);

        $cart = $this->getCart($command->getCartId());
        $cart->id_lang = (int) $language->id;

        try {
            if (false === $cart->update()) {
                throw new CartException('Failed to update cart language');
            }
        } catch (PrestaShopException $e) {
            throw new CartException(sprintf('An error occurred while trying to update language for cart with id "%s"', $cart->id));
        }
    }

    /**
     * @param LanguageId $languageId
     *
     * @return Language
     *
     * @throws LanguageException
     * @throws LanguageNotFoundException
     */
    private function getLanguageObject(LanguageId $languageId): Language
    {
        try {
            $lang = new Language($languageId->getValue());
        } catch (PrestaShopException $e) {
            throw new LanguageException(
                sprintf('An error occurred when fetching language object with id %d', $languageId->getValue()),
                $languageId->getValue()
            );
        }

        if ($languageId->getValue() !== $lang->id) {
            throw new LanguageNotFoundException($languageId, sprintf('Language with id "%s" was not found', $languageId->getValue()));
        }

        return $lang;
    }

    /**
     * @param Language $lang
     *
     * @throws LanguageException
     */
    private function assertLanguageIsActive(Language $lang): void
    {
        if (!$lang->active) {
            throw new LanguageException(sprintf('Language with id "%s" is not active', $lang->id), LanguageException::NOT_ACTIVE);
        }
    }
}
