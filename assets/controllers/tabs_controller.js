import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['command', 'output', 'filename'];
  static classes = ['hidden', 'active', 'inactive'];
  static values = {
    autoplay: { type: Boolean, default: true },
    interval: { type: Number, default: 8000 },
  };

  connect() {
    const initialIndex = this.#indexFromFragment() ?? 0;
    this.currentIndex = initialIndex;
    this.show(initialIndex);

    if (this.#indexFromFragment() !== null) {
      this.element.style.scrollMarginTop = '4rem';
      this.element.scrollIntoView({ behavior: 'instant', block: 'start' });
    }

    if (this.autoplayValue) {
      this.startAutoplay();
    }

    window.addEventListener('hashchange', this.#onHashChange);
  }

  disconnect() {
    this.stopAutoplay();
    window.removeEventListener('hashchange', this.#onHashChange);
  }

  select(event) {
    const index = parseInt(event.currentTarget.dataset.index, 10);
    this.stopAutoplay();
    this.show(index);
    this.#updateFragment(index);
  }

  showOnFocus(event) {
    const index = parseInt(event.currentTarget.dataset.index, 10);
    this.stopAutoplay();
    this.show(index);
  }

  show(index) {
    this.currentIndex = index;

    this.commandTargets.forEach((el, i) => {
      const elIndex = parseInt(el.dataset.index, 10);
      const isActive = elIndex === index;

      if (isActive) {
        el.classList.add(...this.activeClasses);
        if (this.hasInactiveClass) {
          el.classList.remove(...this.inactiveClasses);
        }
        // Update ARIA selected state
        el.setAttribute('aria-selected', 'true');
      } else {
        el.classList.remove(...this.activeClasses);
        if (this.hasInactiveClass) {
          el.classList.add(...this.inactiveClasses);
        }
        // Update ARIA selected state
        el.setAttribute('aria-selected', 'false');
      }
    });

    this.outputTargets.forEach((el, i) => {
      if (i === index) {
        el.classList.remove(this.hiddenClass);
      } else {
        el.classList.add(this.hiddenClass);
      }
    });

    if (this.hasFilenameTarget && this.commandTargets[index]) {
      this.filenameTarget.textContent = this.commandTargets[index].dataset.filename || '';
    }
  }

  next() {
    this.show((this.currentIndex + 1) % this.commandTargets.length);
  }

  startAutoplay() {
    this.stopAutoplay();
    this.autoplayTimer = setInterval(() => this.next(), this.intervalValue);
  }

  stopAutoplay() {
    if (this.autoplayTimer) {
      clearInterval(this.autoplayTimer);
      this.autoplayTimer = null;
    }
  }

  #onHashChange = () => {
    const index = this.#indexFromFragment();
    if (index !== null) {
      this.stopAutoplay();
      this.show(index);
      this.element.style.scrollMarginTop = '4rem';
      this.element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  #indexFromFragment() {
    const fragment = window.location.hash.slice(1);
    if (!fragment) {
      return null;
    }

    const index = this.commandTargets.findIndex((el) => el.dataset.fragment === fragment);
    return index >= 0 ? index : null;
  }

  #updateFragment(index) {
    const fragment = this.commandTargets[index]?.dataset.fragment;
    if (fragment) {
      history.replaceState(null, '', `#${fragment}`);
    }
  }
}
