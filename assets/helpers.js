/**
 * @param {Function} fn
 * @param {number} timeout
 */
const debounce = (fn, timeout = 300) => {
  /** @type {number} */
  let timer;

  return (...args) => {
    if (!timer) {
      fn.apply(this, args);
    }

    clearTimeout(timer);

    timer = setTimeout(() => {
      timer = undefined;
    }, timeout);
  };
};

export { debounce };
