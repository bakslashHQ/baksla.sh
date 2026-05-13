import { Controller } from '@hotwired/stimulus';

/**
 * @property {HTMLButtonElement} copyButtonTarget
 * @property {HTMLElement} copyLabelTarget
 * @property {HTMLElement} copyAnnounceTarget
 * @property {string} copyDoneLabelValue
 */
export default class extends Controller {
  static targets = ['copyButton', 'copyLabel', 'copyAnnounce'];
  static values = { copyDoneLabel: String };

  connect() {
    this.originalLabel = this.copyLabelTarget.textContent;
  }

  disconnect() {
    if (this.copyResetTimeout) clearTimeout(this.copyResetTimeout);
  }

  async copyLink(event) {
    event.preventDefault();

    try {
      await navigator.clipboard.writeText(window.location.href);
    } catch {
      return;
    }

    const done = this.copyDoneLabelValue || 'copied';
    this.copyLabelTarget.textContent = done;
    this.copyAnnounceTarget.textContent = done;

    if (this.copyResetTimeout) clearTimeout(this.copyResetTimeout);
    this.copyResetTimeout = setTimeout(() => {
      this.copyLabelTarget.textContent = this.originalLabel;
    }, 1800);
  }
}
