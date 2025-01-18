import { Controller } from '@hotwired/stimulus';

/**
 * @property {HTMLElement} typewriterTarget
 * @property {Boolean} hasTypewriterTarget
 * @property {Array<HTMLElement>} toolTargets
 * @property {String} hiddenClass
 * @property {Array} wordsValue
 */
export default class extends Controller {
  static targets = ['typewriter', 'tool'];
  static classes = ['hidden'];
  static values = {
    words: Array,
  };

  connect() {
    if (!this.hasTypewriterTarget) {
      throw new Error('Missing "typewriter" target.');
    }
    if (!this.toolTargets.length) {
      throw new Error('Missing "tool" targets.');
    }

    let wordIndex = 0;
    let toolIndex = 0;
    let letterIndex = 0;

    let currentWord = this.wordsValue[0];
    let currentTool = 0;

    const type = () => {
      currentWord = this.wordsValue[wordIndex % this.wordsValue.length];

      this.typewriterTarget.textContent = currentWord.substring(0, letterIndex);

      if (letterIndex === currentWord.length) {
        letterIndex = 0;
        wordIndex++;

        this.timeoutIdType = setTimeout(type, 6000);
      } else {
        this.timeoutIdType = setTimeout(type, 80);
      }

      letterIndex++;
    };

    const setTool = () => {
      currentTool = toolIndex % this.toolTargets.length;

      for (let i = 0; i < this.toolTargets.length; i++) {
        if (i === currentTool) {
          this.toolTargets[i].classList.remove(this.hiddenClass);
        } else {
          this.toolTargets[i].classList.add(this.hiddenClass);
        }
      }

      toolIndex++;

      this.timeoutIdTool = setTimeout(setTool, 8000);
    };

    type();
    setTool();
  }

  disconnect() {
    clearTimeout(this.timeoutIdType);
    clearTimeout(this.timeoutIdTool);
  }
}
