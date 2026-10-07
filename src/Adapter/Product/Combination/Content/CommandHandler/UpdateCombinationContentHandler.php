<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Content\CommandHandler;

use Cache;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Content\Repository\CombinationContentRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Content\Command\UpdateCombinationContentCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Content\CommandHandler\UpdateCombinationContentHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use Validate;

#[AsCommandHandler]
final class UpdateCombinationContentHandler implements UpdateCombinationContentHandlerInterface
{
    public function __construct(
        private readonly CombinationRepository $combinationRepository,
        private readonly CombinationContentRepository $combinationContentRepository,
        private readonly ConfigurationInterface $configuration,
    ) {
    }

    public function handle(UpdateCombinationContentCommand $command): void
    {
        $localizedValues = array_map(
            static fn (array $values): array => array_map('strval', $values),
            array_filter([
                'description' => $command->getLocalizedDescriptions(),
                'description_short' => $command->getLocalizedShortDescriptions(),
                'link_rewrite' => $command->getLocalizedLinkRewrites(),
                'meta_description' => $command->getLocalizedMetaDescriptions(),
                'meta_title' => $command->getLocalizedMetaTitles(),
            ], static fn (?array $values): bool => null !== $values)
        );
        if (empty($localizedValues)) {
            return;
        }

        $this->assertValuesAreValid($localizedValues);

        $combinationId = $command->getCombinationId();
        $this->combinationRepository->assertCombinationExists($combinationId);
        $this->combinationContentRepository->update(
            $combinationId,
            $this->combinationRepository->getShopIdsByConstraint($combinationId, $command->getShopConstraint()),
            $localizedValues
        );
        if (isset($localizedValues['link_rewrite'])) {
            Cache::clean('Link::getCombinationLinkRewrite_*_' . $combinationId->getValue());
        }
    }

    /**
     * @param array<string, array<int, string>> $localizedValues
     *
     * @throws ProductConstraintException
     */
    private function assertValuesAreValid(array $localizedValues): void
    {
        $allowIframe = (bool) $this->configuration->get('PS_ALLOW_HTML_IFRAME');
        $rules = [
            'description' => [ProductSettings::MAX_DESCRIPTION_LENGTH, ProductConstraintException::INVALID_DESCRIPTION],
            'description_short' => [ProductSettings::MAX_DESCRIPTION_LENGTH, ProductConstraintException::INVALID_SHORT_DESCRIPTION],
            'link_rewrite' => [ProductSettings::MAX_LINK_REWRITE_LENGTH, ProductConstraintException::INVALID_LINK_REWRITE],
            'meta_description' => [ProductSettings::MAX_META_DESCRIPTION_LENGTH, ProductConstraintException::INVALID_META_DESCRIPTION],
            'meta_title' => [ProductSettings::MAX_META_TITLE_LENGTH, ProductConstraintException::INVALID_META_TITLE],
        ];

        foreach ($localizedValues as $field => $values) {
            [$maxLength, $errorCode] = $rules[$field];
            foreach ($values as $value) {
                $isValid = match ($field) {
                    'description', 'description_short' => Validate::isCleanHtml($value, $allowIframe),
                    'link_rewrite' => '' === $value || Validate::isLinkRewrite($value),
                    default => Validate::isGenericName($value),
                };
                if (!$isValid || mb_strlen($value) > $maxLength) {
                    throw new ProductConstraintException(sprintf('Invalid combination %s "%s"', $field, $value), $errorCode);
                }
            }
        }
    }
}
