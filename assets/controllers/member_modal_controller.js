import { Controller } from '@hotwired/stimulus';

/**
 * @property {HTMLElement} modalTarget
 */
export default class extends Controller {
  static targets = ['modal', 'backdrop'];
  static values = {
    member: String,
  };

  connect() {
    if (!this.hasModalTarget) {
      throw new Error('Missing "modal" target.');
    }
    if (!this.hasBackdropTarget) {
      throw new Error('Missing "backdrop" target.');
    }
  }

  open(member) {
    if (member !== this.memberValue) {
      return;
    }

    this.backdropTarget.classList.add('opacity-100');
    this.modalTarget.classList.add('opacity-100', 'translate-y-0', 'scale-100');
  }

  close(member) {
    if (member !== this.memberValue) {
      return;
    }

    this.backdropTarget.classList.add('opacity-0');
    this.modalTarget.classList.add('opacity-0', 'translate-y-4', 'scale-95');
  }
}
