import { Controller } from '@hotwired/stimulus';

const TRIGGER_KEY = '/';
const TEXT_ENTRY_SELECTOR = 'input, textarea, select, [contenteditable=true]';
const SELECTED_CLASS = 'bg-ink/10';

/**
 * @property {HTMLDialogElement} dialogTarget
 * @property {HTMLInputElement} inputTarget
 * @property {Array<HTMLElement>} itemTargets
 * @property {Array<HTMLElement>} sectionTargets
 * @property {HTMLElement} emptyTarget
 */
export default class extends Controller {
  static targets = ['dialog', 'input', 'item', 'empty', 'section'];

  connect() {
    this.selectedIndex = 0;

    this.itemTargets.forEach((item, index) => {
      if (!item.id) item.id = `cmd-palette-option-${index}`;
    });

    this.onKeyDown = (e) => {
      if (e.key !== TRIGGER_KEY) return;
      if (e.target?.closest?.(TEXT_ENTRY_SELECTOR)) return;
      e.preventDefault();
      this.open();
    };
    document.addEventListener('keydown', this.onKeyDown);

    // Drop focus after close so a stray Enter doesn't re-trigger the opener button.
    this.onDialogClose = () => {
      this.reset();
      if (document.activeElement instanceof HTMLElement) {
        document.activeElement.blur();
      }
    };
    this.dialogTarget.addEventListener('close', this.onDialogClose);
  }

  disconnect() {
    document.removeEventListener('keydown', this.onKeyDown);
    this.dialogTarget.removeEventListener('close', this.onDialogClose);
  }

  open() {
    if (this.dialogTarget.open) return;
    this.dialogTarget.showModal();
    this.selectedIndex = 0;
    this.updateSelection();
    this.inputTarget.focus();
  }

  close() {
    if (!this.dialogTarget.open) return;
    this.dialogTarget.close();
  }

  closeOnBackdrop(event) {
    if (event.target === event.currentTarget) event.currentTarget.close();
  }

  filter() {
    const query = this.inputTarget.value.trim().toLowerCase();
    let visible = 0;
    this.itemTargets.forEach((item) => {
      const match = !query || item.textContent.toLowerCase().includes(query);
      item.classList.toggle('hidden', !match);
      if (match) visible += 1;
    });
    this.sectionTargets.forEach((section) => {
      const hasVisible = section.querySelector('[data-command-palette-target="item"]:not(.hidden)');
      section.classList.toggle('hidden', !hasVisible);
    });
    this.emptyTarget.classList.toggle('hidden', visible > 0);
    this.selectedIndex = 0;
    this.updateSelection();
  }

  navigate(event) {
    if (event.key === 'ArrowDown') {
      event.preventDefault();
      this.moveSelection(1);
    } else if (event.key === 'ArrowUp') {
      event.preventDefault();
      this.moveSelection(-1);
    } else if (event.key === 'Enter') {
      event.preventDefault();
      this.activateSelected();
    }
  }

  reset() {
    this.inputTarget.value = '';
    this.itemTargets.forEach((item) => {
      item.classList.remove('hidden');
    });
    this.sectionTargets.forEach((section) => {
      section.classList.remove('hidden');
    });
    this.emptyTarget.classList.add('hidden');
    this.selectedIndex = 0;
    this.updateSelection();
  }

  moveSelection(delta) {
    const visible = this.visibleItems();
    if (visible.length === 0) return;
    this.selectedIndex = (this.selectedIndex + delta + visible.length) % visible.length;
    this.updateSelection();
  }

  activateSelected() {
    const visible = this.visibleItems();
    if (visible.length === 0) return;

    const trigger = visible[this.selectedIndex]?.querySelector('a');
    if (!trigger) return;

    trigger.click();
    this.close();
  }

  updateSelection() {
    const visible = this.visibleItems();
    this.itemTargets.forEach((item) => {
      item.querySelector('a')?.classList.remove(SELECTED_CLASS);
      item.setAttribute('aria-selected', 'false');
    });
    const activeItem = visible[this.selectedIndex];
    const activeLink = activeItem?.querySelector('a');
    if (activeLink) activeLink.classList.add(SELECTED_CLASS);
    if (activeItem) {
      activeItem.setAttribute('aria-selected', 'true');
      this.inputTarget.setAttribute('aria-activedescendant', activeItem.id);
    } else {
      this.inputTarget.setAttribute('aria-activedescendant', '');
    }
  }

  visibleItems() {
    return this.itemTargets.filter((item) => !item.classList.contains('hidden'));
  }
}
