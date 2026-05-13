import { Controller } from '@hotwired/stimulus';

/**
 * @property {Array<HTMLElement>} tabTargets
 * @property {Array<HTMLElement>} panelTargets
 */
export default class extends Controller {
  static targets = ['tab', 'panel'];
  static values = { active: { type: Number, default: 0 } };

  initialize() {
    this.desktop = window.matchMedia('(min-width: 64rem)');
    this.onBreakpointChange = () => this.render();
  }

  connect() {
    this.desktop.addEventListener('change', this.onBreakpointChange);
    this.render();
  }

  disconnect() {
    this.desktop.removeEventListener('change', this.onBreakpointChange);
  }

  select(event) {
    this.activeValue = Number(event.params.index);
  }

  navigate(event) {
    const tabs = this.tabTargets;
    const current = tabs.indexOf(event.currentTarget);
    if (current === -1) return;

    let next;
    if (event.key === 'ArrowDown' || event.key === 'ArrowRight') next = (current + 1) % tabs.length;
    else if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') next = (current - 1 + tabs.length) % tabs.length;
    else if (event.key === 'Home') next = 0;
    else if (event.key === 'End') next = tabs.length - 1;
    else return;

    event.preventDefault();
    this.activeValue = next;
    tabs[next].focus();
  }

  activeValueChanged() {
    this.render();
  }

  render() {
    const isDesktop = this.desktop.matches;
    this.tabTargets.forEach((tab, i) => {
      const active = i === this.activeValue;
      tab.toggleAttribute('data-active', active);
      tab.setAttribute('aria-selected', active ? 'true' : 'false');
      tab.tabIndex = active ? 0 : -1;
    });
    this.panelTargets.forEach((panel, i) => {
      const active = i === this.activeValue;
      panel.toggleAttribute('data-active', active);
      panel.inert = isDesktop && !active;
    });
  }
}
