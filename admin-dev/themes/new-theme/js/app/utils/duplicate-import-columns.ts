/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export interface ImportColumnSelection {
  /**
   * 1 based position of the column in the imported file, as the merchant sees it.
   */
  column: number;

  /**
   * Value of the selected option, which is what makes two columns duplicates.
   */
  value: string;

  /**
   * Label of the selected option, which is what the merchant recognises.
   */
  label: string;
}

/**
 * Groups the column selections that share the same value, keeping the order in which the
 * values first appear so the message reads left to right like the table above it.
 */
export const findDuplicateImportColumns = (
  selections: ImportColumnSelection[],
): ImportColumnSelection[][] => {
  const byValue = new Map<string, ImportColumnSelection[]>();

  selections.forEach((selection: ImportColumnSelection) => {
    const group = byValue.get(selection.value);

    if (group) {
      group.push(selection);
    } else {
      byValue.set(selection.value, [selection]);
    }
  });

  return Array.from(byValue.values()).filter(
    (group: ImportColumnSelection[]) => group.length > 1,
  );
};

/**
 * Renders the duplicates as "Label (3, 7); Other label (2, 9)". The shape carries no prose,
 * so it needs no translation of its own: the sentence next to it already says what it means.
 */
export const describeDuplicateImportColumns = (
  selections: ImportColumnSelection[],
): string => findDuplicateImportColumns(selections)
  .map((group: ImportColumnSelection[]) => `${group[0].label} (${group
    .map((selection: ImportColumnSelection) => selection.column)
    .join(', ')})`)
  .join('; ');
