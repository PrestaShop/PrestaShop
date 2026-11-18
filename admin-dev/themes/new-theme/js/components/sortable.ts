/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export interface SortableOptions {
  // Selector matched against the direct children of the container
  items: string;
  // Selector of the only part of an item that starts a drag (the whole item when omitted)
  handle?: string;
  // Selector of elements that never start a drag
  cancel?: string;
  draggingClass?: string;
  onStart?: (item: HTMLElement) => void;
  // Indexes are positions among the children of the container, only called when the position changed.
  // When a promise is returned no other drag starts until it settles, and the item is put back if it is rejected
  onDrop?: (item: HTMLElement, oldIndex: number, newIndex: number) => void | Promise<unknown>;
}

interface Origin {
  item: HTMLElement;
  nextSibling: Element | null;
  index: number;
}

const DRAG_EVENTS = ['dragstart', 'dragenter', 'dragover', 'dragleave', 'drop', 'dragend'];

/**
 * Reorders the children of a container with the HTML drag and drop API,
 * the dragged item is moved in the DOM while it is dragged.
 */
export default class Sortable {
  private readonly container: HTMLElement;

  private readonly options: SortableOptions;

  private armedItem: HTMLElement | null = null;

  private origin: Origin | null = null;

  private pendingDrop = false;

  private lastTarget: HTMLElement | null = null;

  private dropped = false;

  constructor(container: HTMLElement, options: SortableOptions) {
    this.container = container;
    this.options = {
      cancel: 'input, textarea, button, select, option',
      ...options,
    };

    this.container.addEventListener('pointerdown', this.onPointerDown);
    // Capture phase: a sort must not reach the other drop listeners of the container (a file dropzone for example)
    DRAG_EVENTS.forEach((type) => this.container.addEventListener(type, this.onDragEvent, true));
  }

  destroy(): void {
    this.disarm();
    this.container.removeEventListener('pointerdown', this.onPointerDown);
    DRAG_EVENTS.forEach((type) => this.container.removeEventListener(type, this.onDragEvent, true));
  }

  // An item is only draggable for the duration of a press that is allowed to drag it
  private onPointerDown = (event: PointerEvent): void => {
    const target = <Element>event.target;
    this.disarm();

    if (this.pendingDrop || event.button !== 0 || (this.options.cancel && target.closest(this.options.cancel))) {
      return;
    }

    if (this.options.handle) {
      const handle = target.closest(this.options.handle);

      if (!handle || !this.container.contains(handle)) {
        return;
      }
    }

    const item = this.findItem(target);

    if (!item) {
      return;
    }

    // Images and links are draggable on their own and would be dragged instead of the item
    item.querySelectorAll('img, a[href]').forEach((element) => element.setAttribute('draggable', 'false'));
    item.setAttribute('draggable', 'true');
    this.armedItem = item;
    document.addEventListener('pointerup', this.disarm);
  };

  private onDragEvent = (event: Event): void => {
    const dragEvent = <DragEvent>event;

    if (event.type === 'dragstart') {
      this.onDragStart(dragEvent);

      return;
    }

    if (!this.origin) {
      return;
    }

    event.stopPropagation();

    if (event.type === 'dragend') {
      this.onDragEnd();

      return;
    }

    // Makes the container a valid drop target
    event.preventDefault();

    if (event.type === 'dragover') {
      this.onDragOver(dragEvent);
    } else if (event.type === 'drop') {
      // A quick release is not always preceded by a dragover at the same place
      this.onDragOver(dragEvent);
      this.dropped = true;
    }
  };

  private onDragStart(event: DragEvent): void {
    const item = this.armedItem;
    const {dataTransfer} = event;

    if (!item || event.target !== item || !dataTransfer) {
      return;
    }

    event.stopPropagation();
    dataTransfer.effectAllowed = 'move';
    // Firefox does not start a drag without data
    dataTransfer.setData('text/plain', '');

    this.origin = {item, nextSibling: item.nextElementSibling, index: this.indexOf(item)};
    this.lastTarget = null;
    this.dropped = false;

    const {draggingClass} = this.options;

    if (draggingClass) {
      // Added after the browser took its snapshot of the item, which is the image following the pointer
      window.requestAnimationFrame(() => {
        if (this.origin && this.origin.item === item) {
          item.classList.add(draggingClass);
        }
      });
    }

    if (this.options.onStart) {
      this.options.onStart(item);
    }
  }

  private onDragOver(event: DragEvent): void {
    if (!this.origin) {
      return;
    }

    const {dataTransfer} = event;

    if (dataTransfer) {
      dataTransfer.dropEffect = 'move';
    }

    const {item} = this.origin;
    const target = this.findItem(<Element>event.target);

    if (!target || target === item) {
      this.lastTarget = null;

      return;
    }

    // The pointer is still over the item it just swapped with, swapping again would make both flicker
    if (target === this.lastTarget) {
      return;
    }

    this.lastTarget = target;

    if (this.indexOf(item) < this.indexOf(target)) {
      target.after(item);
    } else {
      target.before(item);
    }
  }

  private onDragEnd(): void {
    if (!this.origin) {
      return;
    }

    const {origin} = this;
    this.origin = null;
    this.lastTarget = null;
    this.disarm();

    if (this.options.draggingClass) {
      origin.item.classList.remove(this.options.draggingClass);
    }

    // Released outside of the container or cancelled with the keyboard
    if (!this.dropped) {
      this.restore(origin);

      return;
    }

    const newIndex = this.indexOf(origin.item);

    if (newIndex === origin.index || !this.options.onDrop) {
      return;
    }

    const result = this.options.onDrop(origin.item, origin.index, newIndex);

    if (!(result instanceof Promise)) {
      return;
    }

    // A rejected drop is reverted from its own origin, which is only reliable if nothing moved in the meantime
    this.pendingDrop = true;
    result.then(
      () => {
        this.pendingDrop = false;
      },
      () => {
        this.pendingDrop = false;
        this.restore(origin);
      },
    );
  }

  private disarm = (): void => {
    document.removeEventListener('pointerup', this.disarm);

    if (this.armedItem) {
      this.armedItem.removeAttribute('draggable');
      this.armedItem = null;
    }
  };

  private restore(origin: Origin): void {
    // The item may have been removed while its drop was pending
    if (origin.item.parentElement !== this.container) {
      return;
    }

    const nextSibling = origin.nextSibling && origin.nextSibling.parentElement === this.container
      ? origin.nextSibling
      : null;

    this.container.insertBefore(origin.item, nextSibling);
  }

  private findItem(element: Element | null): HTMLElement | null {
    let node = element;

    while (node && node.parentElement !== this.container) {
      node = node.parentElement;
    }

    return node instanceof HTMLElement && node.matches(this.options.items) ? node : null;
  }

  private indexOf(item: HTMLElement): number {
    return Array.prototype.indexOf.call(this.container.children, item);
  }
}
