(function () {
  const tabs = document.querySelectorAll('[data-tab]');
  if (tabs.length) {
    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        const target = tab.getAttribute('data-tab');
        tabs.forEach((t) => t.setAttribute('aria-selected', 'false'));
        tab.setAttribute('aria-selected', 'true');
        document.querySelectorAll('[data-tab-panel]').forEach((panel) => {
          panel.hidden = panel.getAttribute('data-tab-panel') !== target;
        });
      });
    });
  }

  document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-accordion="toggle"]');
    if (!toggle) {
      return;
    }
    const content = toggle.parentElement.querySelector('[data-accordion="content"]');
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
    if (content) {
      content.classList.toggle('is-open');
    }
  });

  const themeToggle = document.querySelector('[data-theme-toggle]');
  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      document.body.classList.toggle('cem-dark');
      const preference = document.body.classList.contains('cem-dark') ? 'dark' : 'light';
      document.dispatchEvent(new CustomEvent('cem-theme-change', { detail: preference }));
    });
  }

  document.querySelectorAll('[data-progress]').forEach((ring) => {
    const value = parseFloat(ring.getAttribute('data-progress')) || 0;
    const dash = Math.min(Math.max(value, 0), 100);
    const path = ring.querySelector('.cem-progress-value');
    if (path) {
      path.setAttribute('stroke-dasharray', `${dash}, 100`);
    }
  });
})();
