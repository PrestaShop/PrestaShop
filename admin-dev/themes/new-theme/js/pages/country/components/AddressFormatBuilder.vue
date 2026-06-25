<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div class="address-format-builder">
    <div class="address-format-builder__header">
      <ul
        class="nav nav-tabs"
        role="tablist"
      >
        <li class="nav-item">
          <button
            type="button"
            class="nav-link"
            :class="{active: mode === 'visual'}"
            role="tab"
            :aria-selected="mode === 'visual'"
            @click.prevent="setMode('visual')"
          >
            <i class="material-icons">view_module</i>
            {{ $t('mode.visual') }}
          </button>
        </li>
        <li class="nav-item">
          <button
            type="button"
            class="nav-link"
            :class="{active: mode === 'raw'}"
            role="tab"
            :aria-selected="mode === 'raw'"
            @click.prevent="setMode('raw')"
          >
            <i class="material-icons">code</i>
            {{ $t('mode.raw') }}
          </button>
        </li>
      </ul>

      <div class="dropdown">
        <button
          type="button"
          class="btn btn-sm btn-outline-secondary dropdown-toggle"
          data-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false"
        >
          <i class="material-icons">restart_alt</i>
          {{ $t('reset.button') }}
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <button
            type="button"
            class="dropdown-item"
            @click.prevent="resetTo(defaultFormat)"
          >
            <i class="material-icons">history</i>
            {{ $t('reset.default') }}
          </button>
          <button
            type="button"
            class="dropdown-item"
            @click.prevent="resetTo(initialValue)"
          >
            <i class="material-icons">save</i>
            {{ $t('reset.lastSaved') }}
          </button>
          <div class="dropdown-divider" />
          <button
            type="button"
            class="dropdown-item text-danger"
            @click.prevent="confirmClear"
          >
            <i class="material-icons">delete_sweep</i>
            {{ $t('reset.clear') }}
          </button>
        </div>
      </div>
    </div>

    <div class="address-format-builder__body">
      <div class="address-format-builder__editor">
        <div
          v-if="mode === 'visual' && missingFields.length > 0"
          class="alert alert-warning"
          role="status"
          aria-live="polite"
        >
          <strong v-if="missingFields.length === 1">{{ $t('banner.missingOne') }}</strong>
          <strong v-else>{{ $t('banner.missingMany', {count: missingFields.length}) }}</strong>
          <span class="address-format-builder__missing-tokens">
            <button
              v-for="field in missingFields"
              :key="field"
              type="button"
              class="btn btn-sm address-format-builder__missing-chip"
              :title="$t('picker.required')"
              @click.stop.prevent="insertField(field)"
            >
              <i class="material-icons">add</i>
              {{ field }}
            </button>
          </span>
          <button
            type="button"
            class="btn btn-link btn-sm address-format-builder__insert-all-action"
            @click.stop.prevent="insertAllMissing"
          >
            {{ $t('banner.insertAll') }} →
          </button>
        </div>

        <div
          v-if="mode === 'visual'"
          class="address-format-builder__lines"
        >
          <template
            v-for="(line, lineIndex) in lines"
            :key="`line-${lineIndex}`"
          >
            <div
              v-if="lineDropTarget && lineDropTarget.index === lineIndex && lineDropTarget.before"
              class="address-format-builder__line-drop-indicator"
              aria-hidden="true"
            />
            <div
              class="address-format-builder__line"
              :class="{
                'is-drag-over': dragOverLine === lineIndex && draggingLine < 0,
                'is-dragging': draggingLine === lineIndex,
                'is-selected': selectedLine === lineIndex,
              }"
              :aria-pressed="selectedLine === lineIndex"
              @click="toggleSelectLine(lineIndex)"
              @dragenter="onLineDragEnter(lineIndex, $event)"
              @dragover="onLineDragOver(lineIndex, $event)"
              @dragleave="onLineDragLeave"
              @drop="onLineDrop(lineIndex, $event)"
            >
              <span
                class="address-format-builder__grip"
                draggable="true"
                :title="$t('lines.dragHint')"
                :aria-label="$t('lines.dragHint')"
                tabindex="0"
                @click.stop
                @dragstart="onLineDragStart(lineIndex, $event)"
                @dragend="onLineDragEnd"
                @keydown="onGripKeydown(lineIndex, $event)"
              >
                <i class="material-icons">drag_indicator</i>
              </span>
              <span class="address-format-builder__line-number">{{ lineIndex + 1 }}</span>
              <div class="address-format-builder__chips">
                <span
                  v-if="line.length === 0"
                  class="address-format-builder__line-empty"
                >{{ $t('lines.empty') }}</span>
                <template
                  v-for="(token, chipIndex) in line"
                  :key="`chip-${lineIndex}-${chipIndex}-${token.raw}`"
                >
                  <span
                    v-if="chipDropTarget
                      && chipDropTarget.lineIndex === lineIndex
                      && chipDropTarget.chipIndex === chipIndex
                      && chipDropTarget.before"
                    class="address-format-builder__chip-drop-indicator"
                    aria-hidden="true"
                  />
                  <span
                    class="tag address-format-builder__chip"
                    :class="{
                      'is-dragging': dragSource && dragSource.kind === 'chip'
                        && dragSource.lineIndex === lineIndex && dragSource.chipIndex === chipIndex,
                      'is-selected': selectedChip
                        && selectedChip.lineIndex === lineIndex && selectedChip.chipIndex === chipIndex,
                    }"
                    draggable="true"
                    @click.stop="toggleSelectChip(lineIndex, chipIndex)"
                    @dragstart="onChipDragStart(lineIndex, chipIndex, $event)"
                    @dragend="onChipDragEnd"
                    @dragover="onChipDragOver(lineIndex, chipIndex, $event)"
                  >
                    <span class="address-format-builder__chip-key">{{ token.object }}</span>
                    <span class="address-format-builder__chip-sep">:</span>
                    <span class="address-format-builder__chip-value">{{ token.field }}</span>
                    <button
                      type="button"
                      class="address-format-builder__chip-close"
                      :aria-label="$t('lines.removeChip')"
                      @click.stop.prevent="removeToken(lineIndex, chipIndex)"
                    >
                      <i class="material-icons">close</i>
                    </button>
                  </span>
                  <span
                    v-if="chipDropTarget
                      && chipDropTarget.lineIndex === lineIndex
                      && chipDropTarget.chipIndex === chipIndex
                      && !chipDropTarget.before"
                    class="address-format-builder__chip-drop-indicator"
                    aria-hidden="true"
                  />
                </template>
              </div>
              <button
                type="button"
                class="btn btn-link btn-sm address-format-builder__line-remove"
                :aria-label="$t('lines.remove')"
                @click.stop.prevent="removeLine(lineIndex)"
              >
                <i class="material-icons">delete</i>
              </button>
            </div>
            <div
              v-if="lineDropTarget && lineDropTarget.index === lineIndex && !lineDropTarget.before"
              class="address-format-builder__line-drop-indicator"
              aria-hidden="true"
            />
          </template>
          <button
            type="button"
            class="btn btn-outline-secondary btn-sm address-format-builder__add-line"
            @click.prevent="addLine"
          >
            <i class="material-icons">add</i>
            {{ $t('lines.add') }}
          </button>
        </div>

        <div v-else>
          <textarea
            v-model="rawText"
            class="form-control address-format-builder__raw"
            rows="9"
            spellcheck="false"
            @blur="commitRaw"
          />
          <p class="form-text text-muted">
            {{ $t('raw.help') }}
          </p>
        </div>
      </div>

      <div class="address-format-builder__sidebar">
        <div class="address-format-builder__preview">
          <p class="address-format-builder__section-title">
            <i class="material-icons">visibility</i>
            {{ $t('preview.title') }}
          </p>
          <address>
            <div
              v-for="(line, idx) in previewLines"
              :key="`preview-${idx}`"
            >
              {{ line }}
            </div>
            <span
              v-if="previewLines.length === 0"
              class="text-muted font-italic"
            >{{ $t('preview.empty') }}</span>
          </address>
        </div>

        <div class="address-format-builder__picker">
          <p class="address-format-builder__section-title">
            <i class="material-icons">add_box</i>
            {{ $t('picker.title') }}
          </p>
          <div class="form-group">
            <input
              v-model="searchQuery"
              type="search"
              class="form-control"
              :placeholder="$t('picker.search')"
            >
          </div>
          <ul
            class="nav nav-tabs"
            :class="{'is-disabled': isSearching}"
            role="tablist"
          >
            <li
              v-for="obj in pickerObjects"
              :key="obj"
              class="nav-item"
            >
              <button
                type="button"
                class="nav-link"
                :class="{active: obj === activeTab && !isSearching}"
                @click.prevent="selectTab(obj)"
              >
                {{ obj }}
                <span
                  v-if="objectHasMissing(obj)"
                  class="address-format-builder__required-asterisk"
                  aria-hidden="true"
                >*</span>
              </button>
            </li>
          </ul>
          <div class="address-format-builder__picker-body">
            <div
              v-for="group in filteredGroups"
              :key="group.object"
              class="address-format-builder__picker-group"
            >
              <div
                v-if="isSearching"
                class="address-format-builder__picker-group-label"
              >
                {{ group.object }}
              </div>
              <div class="address-format-builder__pills">
                <button
                  v-for="field in group.fields"
                  :key="`${group.object}:${field}`"
                  type="button"
                  class="btn btn-outline-secondary btn-sm address-format-builder__pill"
                  :class="{'is-added': isPlaced(group.object, field)}"
                  :disabled="isPlaced(group.object, field)"
                  :draggable="!isPlaced(group.object, field)"
                  :title="isPlaced(group.object, field) ? $t('picker.alreadyAdded') : ''"
                  @click.stop.prevent="addPickerField(group.object, field)"
                  @dragstart="onPickerDragStart(group.object, field, $event)"
                >
                  <i
                    v-if="isPlaced(group.object, field)"
                    class="material-icons"
                  >check</i>
                  <i
                    v-else
                    class="material-icons"
                  >add</i>
                  {{ field }}
                  <span
                    v-if="isRequired(group.object, field)"
                    class="address-format-builder__required-asterisk"
                    :title="$t('picker.required')"
                  >*</span>
                </button>
              </div>
            </div>
            <p
              v-if="filteredGroups.length === 0"
              class="text-muted small"
            >
              {{ $t('picker.noMatch') }}
            </p>
          </div>
          <p class="form-text small text-muted address-format-builder__picker-footer">
            <span class="address-format-builder__required-asterisk">*</span>
            <span v-html="requiredFooterHtml" />
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent, PropType} from 'vue';
  import {
    AvailableObjects,
    Line,
    Token,
    parseFormat,
    serializeLines,
    resolveToken,
    renderPreview,
    missingRequired,
    placedFieldKeys,
    preferredRaw,
    SampleData,
  } from '@pages/country/components/addressFormatModel';

  type Mode = 'visual' | 'raw';
  type DragSource =
    | {kind: 'picker', object: string, field: string}
    | {kind: 'chip', lineIndex: number, chipIndex: number}
    | {kind: 'line', lineIndex: number}
    | null;

  interface LineDropTarget {
    index: number;
    before: boolean;
  }

  interface ChipDropTarget {
    lineIndex: number;
    chipIndex: number;
    before: boolean;
  }

  interface SelectedChip {
    lineIndex: number;
    chipIndex: number;
  }

  interface State {
    mode: Mode;
    lines: Line[];
    rawText: string;
    activeTab: string;
    searchQuery: string;
    selectedLine: number;
    selectedChip: SelectedChip | null;
    dragSource: DragSource;
    dragOverLine: number;
    draggingLine: number;
    lineDropTarget: LineDropTarget | null;
    chipDropTarget: ChipDropTarget | null;
  }

  const PICKER_OBJECTS = ['Address', 'Country', 'State', 'Customer', 'Warehouse'];

  export default defineComponent({
    name: 'AddressFormatBuilder',
    props: {
      hiddenInput: {
        type: HTMLInputElement,
        required: true,
      },
      initialValue: {
        type: String,
        default: '',
      },
      objects: {
        type: Object as PropType<AvailableObjects>,
        required: true,
      },
      requiredFields: {
        type: Array as PropType<string[]>,
        default: () => [],
      },
      defaultFormat: {
        type: String,
        default: '',
      },
      sampleData: {
        type: Object as PropType<SampleData>,
        required: true,
      },
      requiredFieldsUrl: {
        type: String,
        default: '',
      },
    },
    data(): State {
      return {
        mode: 'visual',
        lines: parseFormat(this.initialValue),
        rawText: this.initialValue,
        activeTab: 'Address',
        searchQuery: '',
        selectedLine: -1,
        selectedChip: null,
        dragSource: null,
        dragOverLine: -1,
        draggingLine: -1,
        lineDropTarget: null,
        chipDropTarget: null,
      };
    },
    computed: {
      pickerObjects(): string[] {
        return PICKER_OBJECTS.filter((o) => Array.isArray(this.objects[o]));
      },
      isSearching(): boolean {
        return this.searchQuery.trim().length > 0;
      },
      previewLines(): string[] {
        return renderPreview(this.lines, this.sampleData);
      },
      missingFields(): string[] {
        return missingRequired(this.lines, this.requiredFields);
      },
      placedKeys(): Set<string> {
        return placedFieldKeys(this.lines);
      },
      filteredGroups(): {object: string, fields: string[]}[] {
        const term = this.searchQuery.trim().toLowerCase();
        const sources = this.isSearching ? this.pickerObjects : [this.activeTab];

        return sources
          .map((obj) => ({
            object: obj,
            fields: (this.objects[obj] ?? []).filter((f) => !term || f.toLowerCase().includes(term)),
          }))
          .filter((group) => group.fields.length > 0);
      },
      hiddenValue(): string {
        return serializeLines(this.lines);
      },
      requiredFooterHtml(): string {
        const raw = this.$t('picker.requiredFooter') as string;

        return raw
          .replace('[link]', `<a href="${this.requiredFieldsUrl}" target="_blank" rel="noopener noreferrer">`)
          .replace('[/link]', '</a>');
      },
    },
    watch: {
      hiddenValue: {
        immediate: true,
        handler(value: string) {
          this.hiddenInput.value = value;
        },
      },
      mode(value: Mode) {
        if (value === 'raw') {
          this.rawText = serializeLines(this.lines);
        } else {
          this.commitRaw();
        }
      },
    },
    mounted(): void {
      document.addEventListener('click', this.handleDocumentClick);
    },
    beforeUnmount(): void {
      document.removeEventListener('click', this.handleDocumentClick);
    },
    methods: {
      setMode(mode: Mode): void {
        this.mode = mode;
      },
      isPlaced(object: string, field: string): boolean {
        return this.placedKeys.has(`${object}:${field}`);
      },
      /**
       * A pill (object, field) is required when the corresponding required entry
       * resolves to the same (object, field) pair. Bare entries (e.g. `firstname`)
       * resolve to `Address:firstname`, so the asterisk lights up only on the
       * Address tab — not on Customer's `firstname` pill, which would need an
       * explicit `Customer:firstname` entry in the required list.
       */
      isRequired(object: string, field: string): boolean {
        return this.requiredFields.some((req) => {
          const resolved = resolveToken(req);

          return resolved.object === object && resolved.field === field;
        });
      },
      objectHasMissing(object: string): boolean {
        return this.missingFields.some((req) => resolveToken(req).object === object);
      },
      selectTab(obj: string): void {
        this.searchQuery = '';
        this.activeTab = obj;
      },
      addPickerField(object: string, field: string): void {
        if (this.isPlaced(object, field)) {
          return;
        }
        this.appendToken({
          object,
          field,
          raw: preferredRaw(object, field),
        });
      },
      insertField(req: string): void {
        // `req` is a required-field entry (bare → Address, or explicit Object:field).
        this.appendToken(resolveToken(req));
      },
      /**
       * Append a token to the currently-selected row when the user has selected one,
       * otherwise to the last row (or as a new row when the last row is non-empty).
       */
      appendToken(token: Token): void {
        if (this.selectedLine >= 0 && this.selectedLine < this.lines.length) {
          this.lines[this.selectedLine].push(token);
          return;
        }
        const last = this.lines[this.lines.length - 1];

        if (last && last.length === 0) {
          last.push(token);
        } else {
          this.lines.push([token]);
        }
      },
      toggleSelectLine(lineIndex: number): void {
        this.selectedLine = this.selectedLine === lineIndex ? -1 : lineIndex;
        // Selecting a row clears any chip selection — only one selection at a time.
        this.selectedChip = null;
      },
      toggleSelectChip(lineIndex: number, chipIndex: number): void {
        const isSame = this.selectedChip
          && this.selectedChip.lineIndex === lineIndex
          && this.selectedChip.chipIndex === chipIndex;
        this.selectedChip = isSame ? null : {lineIndex, chipIndex};
      },
      /**
       * Document-level click listener: clear the selection whenever the click
       * lands outside any line row. Two kinds of in-component clicks are
       * exempt:
       *   - the row itself → its own @click handler toggles selection
       *   - field-adding actions (picker pills, missing-banner per-field
       *     chips, "Insert all missing") → preserve selection so the merchant
       *     can chain multiple inserts onto the same selected row
       */
      handleDocumentClick(ev: MouseEvent): void {
        const target = ev.target as HTMLElement | null;

        if (!target) {
          return;
        }
        const exempt = [
          '.address-format-builder__line',
          '.address-format-builder__pill',
          '.address-format-builder__missing-chip',
          '.address-format-builder__insert-all-action',
        ];

        if (exempt.some((sel) => target.closest(sel))) {
          return;
        }
        if (this.selectedLine !== -1) {
          this.selectedLine = -1;
        }
        if (this.selectedChip !== null) {
          this.selectedChip = null;
        }
      },
      insertAllMissing(): void {
        [...this.missingFields].forEach((field) => this.insertField(field));
      },
      removeToken(lineIndex: number, chipIndex: number): void {
        this.lines[lineIndex].splice(chipIndex, 1);
        if (this.selectedChip && this.selectedChip.lineIndex === lineIndex) {
          if (this.selectedChip.chipIndex === chipIndex) {
            this.selectedChip = null;
          } else if (this.selectedChip.chipIndex > chipIndex) {
            this.selectedChip = {
              lineIndex,
              chipIndex: this.selectedChip.chipIndex - 1,
            };
          }
        }
      },
      removeLine(lineIndex: number): void {
        this.lines.splice(lineIndex, 1);
        if (this.lines.length === 0) {
          this.lines.push([]);
        }
        if (this.selectedLine === lineIndex) {
          this.selectedLine = -1;
        } else if (this.selectedLine > lineIndex) {
          this.selectedLine -= 1;
        }
        if (this.selectedChip && this.selectedChip.lineIndex === lineIndex) {
          this.selectedChip = null;
        } else if (this.selectedChip && this.selectedChip.lineIndex > lineIndex) {
          this.selectedChip = {
            lineIndex: this.selectedChip.lineIndex - 1,
            chipIndex: this.selectedChip.chipIndex,
          };
        }
      },
      addLine(): void {
        this.lines.push([]);
      },
      moveLine(from: number, to: number): void {
        if (from === to || from < 0 || to < 0 || from >= this.lines.length) {
          return;
        }
        const adjusted = to > from ? to - 1 : to;
        const [moved] = this.lines.splice(from, 1);
        this.lines.splice(Math.max(0, Math.min(this.lines.length, adjusted)), 0, moved);
      },
      /**
       * Move a chip to a target line at a specific insertion index.
       * When `toChip` is null the chip is appended to the end of the destination line.
       * Same-line moves are special-cased so the index doesn't shift after splice.
       */
      moveChip(fromLine: number, fromChip: number, toLine: number, toChip: number | null = null): void {
        if (!this.lines[fromLine] || !this.lines[toLine]) {
          return;
        }
        if (fromLine === toLine) {
          if (toChip === null || toChip === fromChip || toChip === fromChip + 1) {
            return;
          }
          const [token] = this.lines[fromLine].splice(fromChip, 1);
          const adjusted = toChip > fromChip ? toChip - 1 : toChip;
          this.lines[fromLine].splice(adjusted, 0, token);
          return;
        }
        const [token] = this.lines[fromLine].splice(fromChip, 1);

        if (toChip === null) {
          this.lines[toLine].push(token);
        } else {
          this.lines[toLine].splice(toChip, 0, token);
        }
      },
      resetTo(format: string): void {
        this.lines = parseFormat(format);
        this.rawText = format;
      },
      confirmClear(): void {
        // window.confirm is fine here — the dropdown is closed by Bootstrap on item click
        // and we want a lightweight confirmation rather than pulling in a modal for this single case.
        // eslint-disable-next-line no-alert
        if (window.confirm(this.$t('reset.confirm') as string)) {
          this.lines = [[]];
          this.rawText = '';
        }
      },
      commitRaw(): void {
        this.lines = parseFormat(this.rawText);
      },
      onPickerDragStart(object: string, field: string, ev: DragEvent): void {
        if (this.isPlaced(object, field)) {
          ev.preventDefault();
          return;
        }
        ev.dataTransfer?.setData(
          'application/prestashop-address-format-field',
          JSON.stringify({object, field}),
        );
        if (ev.dataTransfer) {
          ev.dataTransfer.effectAllowed = 'copy';
        }
        this.dragSource = {kind: 'picker', object, field};
      },
      onChipDragStart(lineIndex: number, chipIndex: number, ev: DragEvent): void {
        ev.dataTransfer?.setData(
          'application/prestashop-address-format-chip',
          JSON.stringify({lineIndex, chipIndex}),
        );
        if (ev.dataTransfer) {
          ev.dataTransfer.effectAllowed = 'move';
        }
        this.dragSource = {kind: 'chip', lineIndex, chipIndex};
        // The chip is about to land somewhere else — drop the persistent selection
        // so it doesn't reference a stale (line, index) pair after the drop.
        this.selectedChip = null;
      },
      onChipDragEnd(): void {
        this.dragSource = null;
        this.dragOverLine = -1;
        this.chipDropTarget = null;
      },
      /**
       * Compute whether the cursor is on the left or right half of the hovered chip
       * so the insertion indicator (and the eventual drop) lands at the right index.
       */
      onChipDragOver(lineIndex: number, chipIndex: number, ev: DragEvent): void {
        if (!ev.dataTransfer?.types.includes('application/prestashop-address-format-chip')) {
          return;
        }
        ev.preventDefault();
        const rect = (ev.currentTarget as HTMLElement).getBoundingClientRect();
        const before = (ev.clientX - rect.left) < rect.width / 2;
        this.chipDropTarget = {lineIndex, chipIndex, before};
      },
      onLineDragStart(lineIndex: number, ev: DragEvent): void {
        ev.dataTransfer?.setData(
          'application/prestashop-address-format-line',
          String(lineIndex),
        );
        if (ev.dataTransfer) {
          ev.dataTransfer.effectAllowed = 'move';
        }
        this.dragSource = {kind: 'line', lineIndex};
        this.draggingLine = lineIndex;
      },
      onLineDragEnd(): void {
        this.dragSource = null;
        this.draggingLine = -1;
        this.lineDropTarget = null;
      },
      onLineDragEnter(lineIndex: number, ev: DragEvent): void {
        if (this.draggingLine !== -1) {
          return;
        }
        const types = ev.dataTransfer?.types ?? [];

        if (
          types.includes('application/prestashop-address-format-field')
          || types.includes('application/prestashop-address-format-chip')
        ) {
          this.dragOverLine = lineIndex;
        }
      },
      onLineDragOver(lineIndex: number, ev: DragEvent): void {
        const types = ev.dataTransfer?.types ?? [];
        const isLineDrag = types.includes('application/prestashop-address-format-line');
        const isFieldDrag = types.includes('application/prestashop-address-format-field');
        const isChipDrag = types.includes('application/prestashop-address-format-chip');

        if (!isLineDrag && !isFieldDrag && !isChipDrag) {
          return;
        }
        ev.preventDefault();
        if (ev.dataTransfer) {
          ev.dataTransfer.dropEffect = isFieldDrag ? 'copy' : 'move';
        }

        if (isLineDrag) {
          // Decide whether to land before or after the hovered row based on cursor Y.
          const rect = (ev.currentTarget as HTMLElement).getBoundingClientRect();
          const before = (ev.clientY - rect.top) < rect.height / 2;
          this.lineDropTarget = {index: lineIndex, before};
        } else {
          this.dragOverLine = lineIndex;
        }
      },
      onLineDragLeave(): void {
        this.dragOverLine = -1;
      },
      onLineDrop(lineIndex: number, ev: DragEvent): void {
        ev.preventDefault();
        const fieldData = ev.dataTransfer?.getData('application/prestashop-address-format-field');
        const chipData = ev.dataTransfer?.getData('application/prestashop-address-format-chip');
        const lineData = ev.dataTransfer?.getData('application/prestashop-address-format-line');

        if (fieldData) {
          const {object, field} = JSON.parse(fieldData) as {object: string, field: string};

          if (!this.isPlaced(object, field)) {
            this.lines[lineIndex].push({
              object,
              field,
              raw: preferredRaw(object, field),
            });
          }
        } else if (chipData) {
          const {lineIndex: fromLine, chipIndex} = JSON.parse(chipData) as {lineIndex: number, chipIndex: number};
          // If the cursor was on a specific chip in the destination line, use that
          // index (with `before` to land left or right of it). Otherwise append.
          let toChip: number | null = null;

          if (this.chipDropTarget && this.chipDropTarget.lineIndex === lineIndex) {
            toChip = this.chipDropTarget.before
              ? this.chipDropTarget.chipIndex
              : this.chipDropTarget.chipIndex + 1;
          }
          this.moveChip(fromLine, chipIndex, lineIndex, toChip);
        } else if (lineData) {
          const fromLine = parseInt(lineData, 10);
          // Use the cursor-derived target so dragging upward lands above the target row.
          const before = this.lineDropTarget?.index === lineIndex && this.lineDropTarget.before;
          const target = before ? lineIndex : lineIndex + 1;
          this.moveLine(fromLine, target);
        }

        this.dragOverLine = -1;
        this.dragSource = null;
        this.draggingLine = -1;
        this.lineDropTarget = null;
        this.chipDropTarget = null;
      },
      onGripKeydown(lineIndex: number, ev: KeyboardEvent): void {
        if (ev.key === 'ArrowUp') {
          ev.preventDefault();
          this.moveLine(lineIndex, lineIndex - 1);
        } else if (ev.key === 'ArrowDown') {
          ev.preventDefault();
          this.moveLine(lineIndex, lineIndex + 2);
        }
      },
    },
  });
</script>

<style lang="scss" scoped>
  .address-format-builder {
    &__header {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-bottom: 1rem;
      border-bottom: 1px solid #ced4da;

      .nav-tabs {
        border-bottom: none;
        margin-bottom: -1px;
      }

      .nav-link i {
        font-size: 1rem;
        vertical-align: -3px;
        margin-right: 0.25rem;
      }

      // Lift the reset dropdown trigger away from the header's border-bottom
      // so the button doesn't visually stick to the line under it.
      .dropdown {
        margin-bottom: 0.375rem;
      }
    }

    &__body {
      display: grid;
      grid-template-columns: 1.15fr 0.85fr;
      gap: 1.5rem;

      @media (max-width: 992px) {
        grid-template-columns: 1fr;
      }
    }

    &__lines {
      border: 1px solid #ced4da;
      padding: 0.375rem;
      background: #ffffff;
    }

    &__line {
      display: flex;
      align-items: center;
      padding: 0.5rem;
      gap: 0.5rem;
      border-radius: 2px;
      cursor: pointer;
      transition: background-color 120ms ease;

      &.is-drag-over {
        background-color: rgba(37, 185, 215, 0.08);
        outline: 1px dashed #25b9d7;
      }

      &.is-dragging {
        opacity: 0.4;
      }

      &.is-selected {
        background-color: rgba(37, 185, 215, 0.12);
        outline: 1px solid rgba(37, 185, 215, 0.5);
      }

      & + & {
        border-top: 1px solid #f1f3f5;
      }
    }

    &__line-drop-indicator {
      height: 2px;
      background-color: #25b9d7;
      margin: 0 0.375rem;
      border-radius: 1px;
      pointer-events: none;
    }

    &__grip {
      cursor: grab;
      color: #6c757d;
      display: inline-flex;
      align-items: center;
      // Stretch full row height + bleed into the line's padding so the entire
      // left edge of the row is a drag handle. +3px each side widens the hit zone.
      align-self: stretch;
      padding: 0.5rem 3px;
      margin: -0.5rem 0;
      transition: color 120ms ease;

      // Suppress the default focus outline (browsers add one because the grip
      // is tabindex="0" for keyboard reorder) and instead colour the icon
      // cyan on hover/focus so the affordance is visible without a hard border.
      &:focus,
      &:focus-visible {
        outline: none;
      }

      &:hover,
      &:focus-visible {
        color: #25b9d7;
      }

      i {
        font-size: 1.125rem;
      }
    }

    &__line-number {
      min-width: 1.25rem;
      color: #6c757d;
      font-size: 0.75rem;
      font-variant-numeric: tabular-nums;
    }

    &__chips {
      display: flex;
      flex-wrap: wrap;
      gap: 0.375rem;
      flex: 1;
      min-height: 2rem;
      align-items: center;
    }

    &__line-empty {
      color: #adb5bd;
      font-style: italic;
      font-size: 0.8125rem;
    }

    &__chip {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      padding: 0.25rem 0.5rem;
      background: #ffffff;
      border: 1px solid #ced4da;
      border-radius: 2px;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
      font-size: 0.8125rem;
      cursor: grab;

      // Highlight the chip's border in cyan both when it's clicked-to-select
      // and while it's being dragged, so the user always sees which chip is
      // their current focus. Dragging also dims it via opacity.
      &.is-selected,
      &.is-dragging {
        border-color: #25b9d7;
      }

      &.is-dragging {
        opacity: 0.4;
      }
    }

    // Thin vertical bar shown between chips while a chip is being dragged inside
    // a line, previewing the future insertion point.
    &__chip-drop-indicator {
      display: inline-block;
      width: 2px;
      align-self: stretch;
      background-color: #25b9d7;
      border-radius: 1px;
      pointer-events: none;
    }

    &__chip-key {
      font-weight: 600;
    }

    &__chip-sep {
      color: #6c757d;
    }

    &__chip-close {
      background: none;
      border: 0;
      padding: 0;
      color: #6c757d;
      cursor: pointer;
      display: inline-flex;

      i {
        font-size: 0.875rem;
      }

      &:hover {
        color: #cd2c1d;
      }
    }

    &__line-remove {
      color: #6c757d;
      padding: 0.25rem;

      i {
        font-size: 1rem;
      }
    }

    &__add-line {
      margin-top: 0.5rem;

      i {
        font-size: 1rem;
        vertical-align: -3px;
      }
    }

    &__sidebar {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    &__preview {
      border: 1px solid #ced4da;
      padding: 1rem;
      background: #f8f9fa;

      address {
        margin-bottom: 0;
        font-style: normal;
      }
    }

    &__picker {
      border: 1px solid #ced4da;
      padding: 1rem;

      .nav-tabs.is-disabled {
        opacity: 0.55;
        pointer-events: none;
      }
    }

    &__section-title {
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: #6c757d;
      margin-bottom: 0.625rem;

      i {
        font-size: 1rem;
        vertical-align: -3px;
        margin-right: 0.25rem;
      }
    }

    &__pills {
      display: flex;
      flex-wrap: wrap;
      gap: 0.375rem;
      margin: 0.5rem 0;
    }

    &__pill {
      i {
        font-size: 1rem;
        vertical-align: -3px;
      }

      &.is-added {
        opacity: 0.55;
      }
    }

    &__picker-group + &__picker-group {
      margin-top: 0.75rem;
    }

    &__picker-group-label {
      font-size: 0.6875rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: #6c757d;
    }

    &__picker-footer {
      margin-top: 0.875rem;
    }

    &__required-asterisk {
      color: #cd2c1d;
      font-weight: 700;
    }

    &__missing-tokens {
      display: inline-flex;
      flex-wrap: wrap;
      gap: 0.25rem;
      margin-left: 0.25rem;
    }

    &__missing-chip {
      display: inline-flex;
      align-items: center;
      gap: 0.125rem;
      background: #ffffff;
      border: 1px solid #ffa000;
      padding: 0.125rem 0.375rem;
      font-family: monospace;
      font-size: 0.75rem;
      cursor: pointer;

      i {
        font-size: 0.875rem;
      }
    }

    &__raw {
      font-family: monospace;
      font-size: 0.875rem;
    }
  }
</style>
