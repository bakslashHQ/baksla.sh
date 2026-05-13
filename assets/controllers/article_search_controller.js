import { Controller } from '@hotwired/stimulus';

// Lowercases and strips diacritics so "cafe" matches "Café", "francois" matches "François", etc.
// NFD splits "é" into "e" + combining accent, then \p{Diacritic} drops the accent.
const normalize = (value) =>
  value
    .toLowerCase()
    .normalize('NFD')
    .replace(/\p{Diacritic}/gu, '');

/**
 * @property {HTMLInputElement} inputTarget
 * @property {HTMLElement[]} itemTargets
 * @property {HTMLElement} emptyTarget
 * @property {HTMLElement} totalLabelTarget
 * @property {HTMLElement} matchesLabelTarget
 * @property {HTMLElement} matchesCountTarget
 * @property {HTMLElement} statusTarget
 * @property {string} emptyValue
 * @property {string} singularValue
 * @property {string} manyTemplateValue
 */
export default class extends Controller {
  static targets = ['input', 'item', 'empty', 'totalLabel', 'matchesLabel', 'matchesCount', 'status'];
  static values = { empty: String, singular: String, manyTemplate: String };

  filter() {
    const query = normalize(this.inputTarget.value.trim());
    let visible = 0;

    for (const item of this.itemTargets) {
      const haystack = normalize(item.dataset.searchHaystack ?? '');
      const match = query === '' || haystack.includes(query);
      item.hidden = !match;
      if (match) visible++;
    }

    this.emptyTarget.hidden = visible > 0;

    const filtering = query !== '';
    this.totalLabelTarget.classList.toggle('!hidden', filtering);
    this.matchesLabelTarget.classList.toggle('!hidden', !filtering);
    if (filtering) {
      this.matchesCountTarget.textContent = String(visible);
    }

    this.statusTarget.textContent = filtering ? this.formatStatus(visible) : '';
  }

  clear() {
    this.inputTarget.value = '';
    this.filter();
  }

  formatStatus(count) {
    if (count === 0) return this.emptyValue;
    if (count === 1) return this.singularValue;
    return this.manyTemplateValue.replace('%count%', String(count));
  }
}
