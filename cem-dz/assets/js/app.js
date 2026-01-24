(function () {
  const defaultState = {
    completedLessons: [],
    completedExercises: [],
    viewedExams: [],
    favorites: [],
    recent: [],
    activityLog: [],
    themePreference: 'light',
  };

  const state = { ...defaultState };
  let canStore = true;
  try {
    const testKey = 'cem_dz_test';
    localStorage.setItem(testKey, '1');
    localStorage.removeItem(testKey);
  } catch (error) {
    canStore = false;
  }

  function loadLocal() {
    if (!canStore) {
      return { ...defaultState };
    }
    try {
      const raw = localStorage.getItem('cem_dz_state');
      return raw ? { ...defaultState, ...JSON.parse(raw) } : { ...defaultState };
    } catch (error) {
      return { ...defaultState };
    }
  }

  function saveLocal(data) {
    if (!canStore) {
      return;
    }
    localStorage.setItem('cem_dz_state', JSON.stringify(data));
  }

  function logActivity(action, id, type) {
    const entry = { action, id, type, timestamp: Date.now() };
    state.activityLog = [entry, ...state.activityLog].slice(0, 200);
  }

  function updateRecent(id, type) {
    const existing = state.recent.filter((item) => item.id !== id || item.type !== type);
    state.recent = [{ id, type, timestamp: Date.now() }, ...existing].slice(0, 10);
  }

  function syncToServer(payload) {
    if (!cemDz?.isLoggedIn) {
      return;
    }
    const formData = new FormData();
    formData.append('action', 'cem_dz_update_user_state');
    formData.append('nonce', cemDz.nonce);
    formData.append('payload', JSON.stringify(payload));
    fetch(cemDz.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: formData });
  }

  function renderRecent() {
    const list = document.querySelector('[data-recent-list]');
    if (!list) {
      return;
    }
    list.innerHTML = '';
    if (!state.recent.length) {
      list.innerHTML = '<div class="cem-card">لا يوجد نشاط حديث.</div>';
      return;
    }
    state.recent.forEach((item) => {
      const card = document.createElement('div');
      card.className = 'cem-card';
      card.textContent = `عنصر رقم ${item.id} (${item.type})`;
      list.appendChild(card);
    });
  }

  function renderDashboard() {
    const favorites = document.querySelector('[data-dashboard="favorites"]');
    if (favorites) {
      favorites.innerHTML = state.favorites.map((id) => `<div>${id}</div>`).join('') || 'لا توجد عناصر.';
    }
    const recent = document.querySelector('[data-dashboard="recent"]');
    if (recent) {
      recent.innerHTML = state.recent.map((item) => `<div>${item.id} (${item.type})</div>`).join('') || 'لا يوجد نشاط.';
    }
    const incomplete = document.querySelector('[data-dashboard="incomplete"]');
    if (incomplete) {
      incomplete.innerHTML = 'راجع الدروس التي لم تكملها بعد.';
    }

    const yearProgress = [
      { label: 'الأولى', value: state.completedLessons.length ? 40 : 10 },
      { label: 'الثانية', value: 30 },
      { label: 'الثالثة', value: 20 },
      { label: 'الرابعة', value: 50 },
    ];
    const activity = Array.from({ length: 7 }, (_, index) => state.activityLog[index]?.timestamp ? 5 : 1);
    document.dispatchEvent(new CustomEvent('cem-chart-data', { detail: { yearProgress, activity } }));
  }

  function updateTheme(preference) {
    state.themePreference = preference;
    if (preference === 'dark') {
      document.body.classList.add('cem-dark');
    } else {
      document.body.classList.remove('cem-dark');
    }
    saveLocal(state);
    syncToServer({ themePreference: preference });
  }

  function mergeLocalToState(localData) {
    Object.keys(defaultState).forEach((key) => {
      if (Array.isArray(localData[key])) {
        state[key] = Array.from(new Set([...state[key], ...localData[key]]));
      } else if (localData[key]) {
        state[key] = localData[key];
      }
    });
  }

  const localData = loadLocal();
  mergeLocalToState(localData);

  if (!canStore) {
    const banner = document.createElement('div');
    banner.className = 'cem-banner';
    banner.textContent = 'التخزين المحلي غير متاح. سيتم حفظ البيانات مؤقتاً فقط.';
    document.querySelector('main')?.prepend(banner);
  }

  if (cemDz?.isLoggedIn) {
    const formData = new FormData();
    formData.append('action', 'cem_dz_get_user_state');
    formData.append('nonce', cemDz.nonce);
    fetch(cemDz.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: formData })
      .then((res) => res.json())
      .then((response) => {
        if (response.success) {
          const serverData = response.data || {};
          Object.keys(defaultState).forEach((key) => {
            if (serverData[key]) {
              state[key] = serverData[key];
            }
          });
          if (localData && JSON.stringify(localData) !== JSON.stringify(defaultState)) {
            const banner = document.createElement('div');
            banner.className = 'cem-banner';
            banner.innerHTML = '<strong>تم العثور على تقدم محلي. هل تريد مزامنته مع حسابك؟</strong>';
            const syncButton = document.createElement('button');
            syncButton.className = 'cem-button cem-button-primary';
            syncButton.textContent = 'مزامنة';
            const dismissButton = document.createElement('button');
            dismissButton.className = 'cem-button';
            dismissButton.textContent = 'تجاهل';
            banner.appendChild(syncButton);
            banner.appendChild(dismissButton);
            document.querySelector('main')?.prepend(banner);
            syncButton.addEventListener('click', () => {
              const syncForm = new FormData();
              syncForm.append('action', 'cem_dz_sync_from_local');
              syncForm.append('nonce', cemDz.nonce);
              syncForm.append('payload', JSON.stringify(localData));
              fetch(cemDz.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: syncForm })
                .then(() => banner.remove());
            });
            dismissButton.addEventListener('click', () => banner.remove());
          }
        }
        renderRecent();
        renderDashboard();
      });
  } else {
    renderRecent();
    renderDashboard();
  }

  document.addEventListener('click', (event) => {
    const resumeButton = event.target.closest('[data-resume]');
    if (resumeButton && state.recent.length) {
      const latest = state.recent[0];
      window.location.href = `${window.location.origin}/?p=${latest.id}`;
      return;
    }
    const completeButton = event.target.closest('[data-toggle-complete]');
    const favoriteButton = event.target.closest('[data-toggle-favorite]');
    const article = event.target.closest('[data-content-id]');
    if (!article) {
      return;
    }
    const id = parseInt(article.getAttribute('data-content-id'), 10);
    const type = article.getAttribute('data-content-type');

    if (completeButton) {
      let listKey = 'completedLessons';
      if (type === 'exercise') {
        listKey = 'completedExercises';
      }
      if (type === 'exam') {
        listKey = 'viewedExams';
      }
      const list = new Set(state[listKey]);
      if (list.has(id)) {
        list.delete(id);
      } else {
        list.add(id);
      }
      state[listKey] = Array.from(list);
      logActivity('complete', id, type);
      updateRecent(id, type);
      saveLocal(state);
      syncToServer({ [listKey]: state[listKey], activityLog: state.activityLog, recent: state.recent });
    }

    if (favoriteButton) {
      const list = new Set(state.favorites);
      if (list.has(id)) {
        list.delete(id);
      } else {
        list.add(id);
      }
      state.favorites = Array.from(list);
      logActivity('favorite', id, type);
      updateRecent(id, type);
      saveLocal(state);
      syncToServer({ favorites: state.favorites, activityLog: state.activityLog, recent: state.recent });
    }
  });

  document.addEventListener('cem-theme-change', (event) => {
    updateTheme(event.detail);
  });

  updateTheme(state.themePreference || 'light');
})();
