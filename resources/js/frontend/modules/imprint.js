const SEL = {
  btn: '[data-imprint="btn"]',
  body: '[data-imprint="body"]',
};

export function init() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest(SEL.btn);
    const body = btn?.parentElement.querySelector(SEL.body);
    if (body) body.hidden = !body.hidden;
  });
}
