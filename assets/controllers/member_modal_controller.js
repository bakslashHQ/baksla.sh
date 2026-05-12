import { Controller } from '@hotwired/stimulus';

const CLOSED_CLASSES = ['opacity-0', 'translate-y-4', 'scale-95'];
const OPEN_CLASSES = ['opacity-100', 'scale-100'];

/**
 * @property {HTMLElement} modalTarget
 * @property {String} memberValue
 * @property {String} prevValue
 * @property {String} nextValue
 */
export default class extends Controller {
  static targets = ['modal'];
  static values = {
    member: String,
    prev: String,
    next: String,
  };

  connect() {
    if (!this.hasModalTarget) {
      throw new Error('Missing "modal" target.');
    }

    // Openers live outside the controller element (reviewer rail, comment headers),
    // so Stimulus actions can't reach them, so bind manually here.
    this.openerHandler = this.open.bind(this);
    this.openers = Array.from(document.querySelectorAll(`[data-open-member-modal="${this.memberValue}"]`));
    for (const opener of this.openers) {
      opener.addEventListener('click', this.openerHandler);
    }
  }

  disconnect() {
    clearTimeout(this.timeoutClose);
    for (const opener of this.openers) {
      opener.removeEventListener('click', this.openerHandler);
    }
  }

  open() {
    this.element.showModal();

    // Force a paint with the initial (closed) state before animating to the open state
    // so the CSS transition actually fires when chaining open() right after close().
    requestAnimationFrame(() => this.#setOpenState());
  }

  close() {
    this.#setClosedState();
    this.timeoutClose = setTimeout(() => this.element.close(), 150);
  }

  onCancel(event) {
    // Prevent the native auto-close so the fade-out animation can play first.
    event.preventDefault();
    this.close();
  }

  prev(event) {
    this.#navigateTo(event, this.prevValue);
  }

  next(event) {
    this.#navigateTo(event, this.nextValue);
  }

  #navigateTo(event, memberId) {
    if (!this.element.open || !memberId) {
      return;
    }

    event.preventDefault();
    clearTimeout(this.timeoutClose);
    this.#setClosedState();
    this.element.close();

    const target = document.querySelector(`dialog[data-member-modal-member-value="${memberId}"]`);
    this.application.getControllerForElementAndIdentifier(target, 'member-modal')?.open();
  }

  #setOpenState() {
    this.modalTarget.classList.add(...OPEN_CLASSES);
    this.modalTarget.classList.remove(...CLOSED_CLASSES);
  }

  #setClosedState() {
    this.modalTarget.classList.add(...CLOSED_CLASSES);
    this.modalTarget.classList.remove(...OPEN_CLASSES);
  }
}
