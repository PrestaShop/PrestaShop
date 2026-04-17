<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Builder\Map;

/**
 * This class is the representation of the last sheet of a translation catalogue : The message itself.
 * A message is composed by the default wording,
 * its translation within the project files (for crowdin or any translation tool),
 * its translation made in the BO interface and stored in DB.
 * If a message has file or user translation, it's considered as translated.
 */
class Message
{
    /**
     * @var string
     */
    private $defaultTranslation;

    /**
     * @var string|null
     */
    private $fileTranslation;

    /**
     * @var string|null
     */
    private $userTranslation;

    public function __construct(string $defaultTranslation)
    {
        $this->defaultTranslation = $defaultTranslation;
    }

    public function getKey(): string
    {
        return $this->defaultTranslation;
    }

    public function setFileTranslation(string $fileTranslation): self
    {
        $this->fileTranslation = $fileTranslation;

        return $this;
    }

    public function setUserTranslation(string $userTranslation): self
    {
        $this->userTranslation = $userTranslation;

        return $this;
    }

    /**
     * Returns whether a message is translated or not.
     * It's TRUE if one of fileTranslation or userTranslation is not null
     */
    public function isTranslated(): bool
    {
        return null !== $this->fileTranslation || null !== $this->userTranslation;
    }

    /**
     * Returns the translated string
     * UserTranslation OR FileTranslation OR Default key
     *
     * @return string
     */
    public function getTranslation(): string
    {
        return $this->userTranslation ?? $this->fileTranslation ?? $this->getKey();
    }

    /**
     * Check if data contains search word.
     *
     * @param array $search
     *
     * @return bool
     */
    public function contains(array $search): bool
    {
        if (empty($search)) {
            return false;
        }

        foreach ($search as $s) {
            if (!$this->containsWord($s)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'default' => $this->defaultTranslation,
            'project' => $this->fileTranslation,
            'user' => $this->userTranslation,
        ];
    }

    private function containsWord(string $s): bool
    {
        $s = strtolower($s);

        return
            str_contains(strtolower($this->defaultTranslation), $s)
            || (null !== $this->fileTranslation && str_contains(strtolower($this->fileTranslation), $s))
            || (null !== $this->userTranslation && str_contains(strtolower($this->userTranslation), $s))
        ;
    }
}
