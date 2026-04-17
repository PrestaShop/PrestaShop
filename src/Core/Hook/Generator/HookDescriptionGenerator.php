<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Hook\Generator;

use PrestaShop\PrestaShop\Core\Hook\HookDescription;
use PrestaShop\PrestaShop\Core\Util\String\StringModifierInterface;
use PrestaShop\PrestaShop\Core\Util\String\StringValidatorInterface;

/**
 * Generates description for hook names.
 */
final class HookDescriptionGenerator implements HookDescriptionGeneratorInterface
{
    /**
     * @var array
     */
    private $hookDescriptions;

    /**
     * @var StringValidatorInterface
     */
    private $stringValidator;

    /**
     * @var StringModifierInterface
     */
    private $stringModifier;

    /**
     * @param array $hookDescriptions
     * @param StringValidatorInterface $stringValidator
     * @param StringModifierInterface $stringModifier
     */
    public function __construct(
        array $hookDescriptions,
        StringValidatorInterface $stringValidator,
        StringModifierInterface $stringModifier
    ) {
        $this->hookDescriptions = $hookDescriptions;
        $this->stringValidator = $stringValidator;
        $this->stringModifier = $stringModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($hookName)
    {
        foreach ($this->hookDescriptions as $hookDescription) {
            $prefix = isset($hookDescription['prefix']) ? $hookDescription['prefix'] : '';
            $suffix = isset($hookDescription['suffix']) ? $hookDescription['suffix'] : '';

            if ($this->stringValidator->startsWithAndEndsWith($hookName, $prefix, $suffix)
                && !$this->stringValidator->doesContainsWhiteSpaces($hookName)
            ) {
                $hookId = $this->extractHookId($hookName, $prefix, $suffix);

                return new HookDescription(
                    $hookName,
                    $this->getTextWithHookId($hookDescription['title'], $hookId),
                    $this->getTextWithHookId($hookDescription['description'], $hookId)
                );
            }
        }

        return new HookDescription(
            $hookName,
            '',
            ''
        );
    }

    /**
     * Removes from hook name id prefix and suffix.
     *
     * @param string $hookName
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    private function extractHookId($hookName, $prefix, $suffix)
    {
        return str_replace([$prefix, $suffix], '', $hookName);
    }

    /**
     * Gets text with replaced hook id.
     *
     * @param string $description
     * @param string $hookId
     *
     * @return string
     */
    private function getTextWithHookId($description, $hookId)
    {
        if (!$this->doesHookDescriptionContainsPlaceholder($description)) {
            return $description;
        }

        $hookIdSplitByCamelCase = $this->stringModifier->splitByCamelCase($hookId);

        $isPlaceholderAsFirstValueInString = $this->doesPlaceholderIsTheFirstElementOfTheDescription($description);

        if ($isPlaceholderAsFirstValueInString) {
            $hookIdSplitByCamelCase = ucfirst($hookIdSplitByCamelCase);
        } else {
            $hookIdSplitByCamelCase = strtolower($hookIdSplitByCamelCase);
        }

        return sprintf($description, $hookIdSplitByCamelCase);
    }

    /**
     * Checks if hook description contains placeholder value.
     *
     * @param string $description
     *
     * @return bool
     */
    private function doesHookDescriptionContainsPlaceholder($description)
    {
        return str_contains($description, '%s');
    }

    /**
     * Checks if placeholder is the first element of the string.
     *
     * @param string $description
     *
     * @return bool
     */
    private function doesPlaceholderIsTheFirstElementOfTheDescription($description)
    {
        return str_starts_with($description, '%s');
    }
}
