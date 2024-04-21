const words = ["experts.", "lovers.", "developers."];
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
  const spaces = '\xa0'.repeat(maxWordsLength - letterIndex + 1);

  document.getElementById("typewriter").textContent = text + spaces;

  if (letterIndex == currentWord.length) {
    letterIndex = 0;
    wordIndex++;

    setTimeout(type, 4000);
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

const onView = (entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.remove("opacity-0");
      entry.target.classList.remove("mt-16");
    } else {
      entry.target.classList.add("opacity-0");
      entry.target.classList.add("mt-16");
    }
  });
};

const copyEmail = () => {
  const emailButton = document.getElementById("email-button");
  const emailText = document.getElementById("email-copied-text");

  emailButton.classList.add("blur");
  emailButton.classList.add("pointer-events-none");
  emailButton.classList.remove("hover:bg-gray-700");
  emailButton.classList.remove("hover:text-white");
  emailText.classList.add("-translate-y-12");
  emailText.classList.remove("opacity-0");

  navigator.clipboard.writeText('hello@baksla.sh');

  setTimeout(() => {
    emailButton.classList.remove("blur");
    emailButton.classList.remove("pointer-events-none");
    emailButton.classList.add("hover:bg-gray-700");
    emailButton.classList.add("hover:text-white");
    emailText.classList.remove("-translate-y-12");
    emailText.classList.add("opacity-0");
  }, 1500)
}

type();
setTool();

const observer = new IntersectionObserver(onView);
observer.observe(document.getElementById("rocket"));
