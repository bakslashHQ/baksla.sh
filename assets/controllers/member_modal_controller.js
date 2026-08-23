import { Controller } from '@hotwired/stimulus';

const VISIBLE_CLASSES = ['opacity-100', 'scale-100'];

// Opening and closing lift the modal and scale it down, while moving between members only slides sideways.
const ENTER_CLASSES = {
  initial: ['opacity-0', 'scale-95', 'translate-y-4'],
  prev: ['opacity-0', '-translate-x-8'],
  next: ['opacity-0', 'translate-x-8'],
};
const LEAVE_CLASSES = {
  initial: ENTER_CLASSES.initial,
  prev: ENTER_CLASSES.next,
  next: ENTER_CLASSES.prev,
};
const RESET_CLASSES = [...new Set([...VISIBLE_CLASSES, ...Object.values(ENTER_CLASSES).flat()])];
const LEAVE_DURATION = 150;

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
    this.openerHandler = () => this.open();
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

  open(direction = 'initial') {
    this.#setState(ENTER_CLASSES[direction]);
    this.element.showModal();

    // Force a paint with the initial (closed) state before animating to the open state
    // so the CSS transition actually fires when chaining open() right after close().
    requestAnimationFrame(() => this.#setState(VISIBLE_CLASSES));
  }

  close() {
    this.#setState(LEAVE_CLASSES.initial);
    this.timeoutClose = setTimeout(() => this.element.close(), this.#leaveDuration());
  }

  onCancel(event) {
    // Prevent the native auto-close so the fade-out animation can play first.
    event.preventDefault();
    this.close();
  }

  prev(event) {
    this.#navigateTo(event, this.prevValue, 'prev');
  }

  next(event) {
    this.#navigateTo(event, this.nextValue, 'next');
  }

  onKeydown(event) {
    if (event.repeat || event.altKey || event.ctrlKey || event.metaKey || event.shiftKey) {
      return;
    }

    if (event.key === 'ArrowLeft') {
      this.prev(event);
    } else if (event.key === 'ArrowRight') {
      this.next(event);
    }
  }

  #navigateTo(event, memberId, direction) {
    if (!this.element.open || !memberId) {
      return;
    }

    event.preventDefault();
    clearTimeout(this.timeoutClose);
    this.#setState(LEAVE_CLASSES[direction]);

    const target = document.querySelector(`dialog[data-member-modal-member-value="${memberId}"]`);

    // Only one dialog can be modal at a time, so the outgoing slide has to finish before the next one opens.
    this.timeoutClose = setTimeout(() => {
      this.element.close();
      this.application.getControllerForElementAndIdentifier(target, 'member-modal')?.open(direction);
    }, this.#leaveDuration());
  }

  // prefers-reduced-motion zeroes the CSS transition, so the wait has to follow it rather than the constant.
  #leaveDuration() {
    const transition = parseFloat(getComputedStyle(this.modalTarget).transitionDuration) * 1000;

    return Math.min(transition, LEAVE_DURATION);
  }

  #setState(classes) {
    this.modalTarget.classList.remove(...RESET_CLASSES);
    this.modalTarget.classList.add(...classes);
  }
}
