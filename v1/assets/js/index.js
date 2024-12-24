const words = ["experts.", "lovers.", "developers.", "advocates."];
const maxWordsLength = Math.max(...words.map((w) => w.length));
const toolsCounts = 3;

let wordIndex = 0;
let toolIndex = 0;
let letterIndex = 0;

let currentWord = words[0];
let currentTool = 0;

const type = () => {
  currentWord = words[wordIndex % words.length];

  const text = currentWord.substring(0, letterIndex);

  document.getElementById("typewriter").textContent = text;

  if (letterIndex == currentWord.length) {
    letterIndex = 0;
    wordIndex++;

    setTimeout(type, 6000);
  } else {
    setTimeout(type, 80);
  }

  letterIndex++;
}

const setTool = () => {
  currentTool = toolIndex % toolsCounts;

  for (let i = 0; i < toolsCounts; i++) {
    if (i === currentTool) {
      document.getElementById(`tool-${i}`)?.classList.remove("hidden");
    } else {
      document.getElementById(`tool-${i}`)?.classList.add("hidden");
    }
  }

  toolIndex++;

  setTimeout(setTool, 8000);
}

const copyEmail = (emailLink) => {
  const emailText = emailLink.firstElementChild;
  const emailCopyText = emailLink.lastElementChild;

  emailText.classList.add("blur", "pointer-events-none");
  emailCopyText.classList.add(emailCopyText.dataset.translateUp);
  emailCopyText.classList.remove("opacity-0");

  navigator.clipboard.writeText('hello@baksla.sh');

  setTimeout(() => {
    emailText.classList.remove("blur", "pointer-events-none");
    emailCopyText.classList.remove(emailCopyText.dataset.translateUp);
    emailCopyText.classList.add("opacity-0");
  }, 1500);
}

const backgroundParallax = () => {
  const code = document.getElementById('code');
  const nextElement = code.nextElementSibling;


  window.onscroll = function () {
    const scrolledPercentage = window.scrollY / (document.documentElement.scrollHeight - document.documentElement.clientHeight)

    code.style.backgroundPositionY = -1 * (scrolledPercentage * 1000) + 'px';
  }

  window.onload = function () {
    code.style.width = nextElement.offsetWidth + 'px';
    code.style.height = nextElement.offsetHeight + 'px';
  }

  window.onresize = function () {
    code.style.width = nextElement.offsetWidth + 'px';
    code.style.height = nextElement.offsetHeight + 'px';
  }
}

type();
setTool();
backgroundParallax();
