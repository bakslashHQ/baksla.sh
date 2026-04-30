import { Controller } from '@hotwired/stimulus';

const REVEAL_INTERVAL_MS = 520;
const COUNT_UP_DURATION_MS = 600;
const INTERACTIVE_SELECTOR = 'button, a, input, textarea, select, summary, [role=dialog], [contenteditable=true]';

/**
 * @property {Array<HTMLElement>} lineTargets
 * @property {Array<HTMLElement>} numberTargets
 * @property {HTMLElement} finaleTarget
 * @property {HTMLElement} promptCursorTarget
 * @property {HTMLElement} runAreaTarget
 * @property {HTMLElement} runHintTarget
 */
export default class extends Controller {
  static targets = ['line', 'number', 'finale', 'promptCursor', 'runArea', 'runHint'];

  connect() {
    this.timeouts = [];
    this.rafByNode = new Map();
    this.started = false;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      this.revealAllInstantly();
      return;
    }

    // Enter-key shortcut only when the "or press ⏎" hint is visible
    if (getComputedStyle(this.runHintTarget).display === 'none') return;

    this.onKeyDown = (e) => {
      if (e.key !== 'Enter') return;
      // Let interactive elements keep their native Enter behavior.
      if (e.target?.closest?.(INTERACTIVE_SELECTOR)) return;
      e.preventDefault();
      this.run();
    };
    document.addEventListener('keydown', this.onKeyDown);
  }

  disconnect() {
    if (this.onKeyDown) document.removeEventListener('keydown', this.onKeyDown);
    this.timeouts.forEach(clearTimeout);
    this.rafByNode.forEach((id) => {
      cancelAnimationFrame(id);
    });
  }

  run() {
    // Synchronous guard: button click and document keydown can fire in the same tick.
    if (this.started) return;
    this.started = true;

    if (this.onKeyDown) {
      document.removeEventListener('keydown', this.onKeyDown);
      this.onKeyDown = null;
    }

    // Reset numbers to zero now — server-rendered values stay in the DOM until run fires,
    // so no-JS / pre-hydration visitors still get real numbers from the sr-only list.
    this.numberTargets.forEach((node) => {
      if (this.numericValue(node) !== null) {
        node.textContent = `0${node.dataset.suffix ?? ''}`;
      }
    });

    this.lineTargets.forEach((line, i) => {
      this.schedule(() => {
        if (i === 0) this.clearIntro();
        line.classList.remove('hidden');
        this.countUp(this.numberTargets[i]);
      }, REVEAL_INTERVAL_MS * i);
    });

    // Finale appears after the last count-up settles.
    this.schedule(() => this.finaleTarget.classList.remove('hidden'), REVEAL_INTERVAL_MS * this.lineTargets.length + COUNT_UP_DURATION_MS);
  }

  revealAllInstantly() {
    this.clearIntro();
    this.lineTargets.forEach((line) => {
      line.classList.remove('hidden');
    });
    this.finaleTarget.classList.remove('hidden');
    // Server-rendered values are already the real values: no reset needed.
  }

  clearIntro() {
    this.promptCursorTarget.remove();
    this.runAreaTarget.remove();
  }

  countUp(node) {
    if (!node) return;
    const value = this.numericValue(node);
    const suffix = node.dataset.suffix ?? '';
    if (value === null) {
      // Non-numeric (e.g. "∞"): just show the literal value.
      node.textContent = `${node.dataset.value}${suffix}`;
      return;
    }
    const start = performance.now();
    let lastShown = null;
    const tick = (now) => {
      const t = Math.min(1, (now - start) / COUNT_UP_DURATION_MS);
      const eased = 1 - (1 - t) ** 3;
      const current = Math.round(value * eased);
      if (current !== lastShown) {
        node.textContent = `${current}${suffix}`;
        lastShown = current;
      }
      if (t < 1) this.rafByNode.set(node, requestAnimationFrame(tick));
      else this.rafByNode.delete(node);
    };
    this.rafByNode.set(node, requestAnimationFrame(tick));
  }

  schedule(fn, delay) {
    this.timeouts.push(setTimeout(fn, delay));
  }

  numericValue(node) {
    const parsed = Number.parseInt(node.dataset.value, 10);
    return Number.isFinite(parsed) ? parsed : null;
  }
}
