<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$documentationLink = 'https://devdocs.prestashop-project.org/';
$blogLink = 'https://build.prestashop-project.org/';

return [
    'links' => [
        'documentation' => $documentationLink,
    ],
    'header.links' => [
        $documentationLink => $this->translator->trans('Documentation', array(), 'Install'),
        $blogLink => $this->translator->trans('Blog', array(), 'Install'),
    ],
    'footer.links' => [
        'http://prestashop-project.org/' => 'PrestaShop Project',
        $documentationLink => $this->translator->trans('Documentation', array(), 'Install'),
    ],
];
