  </div>
  </div>
  <script>
    (function () {
      const toggleBtn = document.querySelector('[data-admin-sidebar-toggle]');
      const body = document.body;
      if (!toggleBtn || !body) {
        return;
      }
      const icon = toggleBtn.querySelector('[data-admin-sidebar-icon]');
      const storageKey = 'scriptloadedAdminSidebarCollapsed';
      const getStored = () => {
        try {
          return localStorage.getItem(storageKey) === '1';
        } catch (error) {
          return false;
        }
      };
      const setStored = (value) => {
        try {
          localStorage.setItem(storageKey, value ? '1' : '0');
        } catch (error) {
          /* noop */
        }
      };
      const applyState = (collapsed) => {
        body.classList.toggle('admin-sidebar-collapsed', collapsed);
        if (icon) {
          icon.textContent = collapsed ? 'chevron_right' : 'chevron_left';
        }
        toggleBtn.setAttribute('aria-pressed', collapsed ? 'true' : 'false');
        setStored(collapsed);
      };
      applyState(getStored());
      toggleBtn.addEventListener('click', () => {
        const nextState = !body.classList.contains('admin-sidebar-collapsed');
        applyState(nextState);
      });
    })();

    (function () {
      const popovers = document.querySelectorAll('[data-admin-popover]');
      if (!popovers.length) {
        return;
      }
      const closeAll = () => {
        popovers.forEach((group) => {
          const panel = group.querySelector('[data-popover-panel]');
          const trigger = group.querySelector('[data-popover-trigger]');
          panel?.classList.remove('is-open');
          trigger?.setAttribute('aria-expanded', 'false');
        });
      };
      popovers.forEach((group) => {
        const trigger = group.querySelector('[data-popover-trigger]');
        const panel = group.querySelector('[data-popover-panel]');
        if (!trigger || !panel) {
          return;
        }
        trigger.addEventListener('click', (event) => {
          event.stopPropagation();
          const isOpen = panel.classList.contains('is-open');
          closeAll();
          if (!isOpen) {
            panel.classList.add('is-open');
            trigger.setAttribute('aria-expanded', 'true');
          }
        });
        panel.addEventListener('click', (event) => {
          event.stopPropagation();
        });
      });
      document.addEventListener('click', closeAll);
      document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
          closeAll();
        }
      });
    })();

    (function () {
      const openBtn = document.querySelector('[data-admin-mobile-menu-open]');
      const overlay = document.querySelector('[data-admin-mobile-menu-overlay]');
      const panel = document.querySelector('[data-admin-mobile-menu-panel]');
      const closeBtn = document.querySelector('[data-admin-mobile-menu-close]');
      if (!openBtn || !overlay || !panel) {
        return;
      }
      const closeMenu = () => {
        overlay.classList.add('pointer-events-none');
        overlay.classList.add('opacity-0');
        overlay.classList.remove('opacity-100');
        panel.classList.add('-translate-x-full');
      };
      const openMenu = () => {
        overlay.classList.remove('pointer-events-none');
        overlay.classList.remove('opacity-0');
        overlay.classList.add('opacity-100');
        panel.classList.remove('-translate-x-full');
      };
      openBtn.addEventListener('click', openMenu);
      overlay.addEventListener('click', closeMenu);
      closeBtn?.addEventListener('click', closeMenu);
      document.querySelectorAll('[data-admin-mobile-menu-link]').forEach((link) => {
        link.addEventListener('click', closeMenu);
      });
      document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
          closeMenu();
        }
      });
    })();

    (function () {
      const tables = document.querySelectorAll('[data-admin-table]');
      if (!tables.length) {
        return;
      }
      tables.forEach((table) => {
        const headers = Array.from(table.querySelectorAll('thead th')).map((th) => th.textContent.trim());
        table.querySelectorAll('tbody tr').forEach((row) => {
          row.querySelectorAll('td').forEach((cell, index) => {
            if (!cell.getAttribute('data-label')) {
              const label = headers[index] || '';
              if (label) {
                cell.setAttribute('data-label', label);
              }
            }
          });
        });
      });
    })();
  </script>
  </body>
  </html>
