window.addEventListener("DOMContentLoaded", () => {
  const toggles = document.querySelectorAll("[data-mobile-menu-toggle]");
  if (!toggles.length) {
    return;
  }

  const focusableSelectors = [
    "a[href]",
    "button:not([disabled])",
    "input:not([disabled])",
    "select:not([disabled])",
    "textarea:not([disabled])",
  ].join(",");

  toggles.forEach((toggle) => {
    const targetId = toggle.getAttribute("data-mobile-menu-toggle");
    if (!targetId) {
      return;
    }

    const panel = document.querySelector(
      `[data-mobile-menu-panel="${targetId}"]`
    );
    const overlay = document.querySelector(
      `[data-mobile-menu-overlay="${targetId}"]`
    );
    if (!panel || !overlay) {
      return;
    }

    const closeButtons = panel.querySelectorAll("[data-mobile-menu-close]");
    const openIcon = toggle.querySelector('[data-menu-icon="open"]');
    const closeIcon = toggle.querySelector('[data-menu-icon="close"]');
    let isOpen = false;

    const setPanelState = (open) => {
      isOpen = open;
      toggle.setAttribute("aria-expanded", String(open));
      panel.classList.toggle("pointer-events-none", !open);
      panel.classList.toggle("opacity-0", !open);
      panel.classList.toggle("-translate-y-4", !open);
      panel.classList.toggle("scale-95", !open);
      overlay.classList.toggle("pointer-events-none", !open);
      overlay.classList.toggle("opacity-0", !open);
      openIcon?.classList.toggle("hidden", open);
      closeIcon?.classList.toggle("hidden", !open);
      if (open) {
        const focusable = panel.querySelector(focusableSelectors);
        focusable?.focus({ preventScroll: true });
      } else {
        toggle.focus();
      }
    };

    const closeMenu = () => {
      if (!isOpen) {
        return;
      }
      setPanelState(false);
      document.removeEventListener("keydown", handleKeydown);
    };

    const handleKeydown = (event) => {
      if (event.key === "Escape") {
        closeMenu();
      }
    };

    toggle.addEventListener("click", () => {
      setPanelState(!isOpen);
      if (isOpen) {
        document.addEventListener("keydown", handleKeydown);
      } else {
        document.removeEventListener("keydown", handleKeydown);
      }
    });

    overlay.addEventListener("click", closeMenu);
    closeButtons.forEach((btn) => btn.addEventListener("click", closeMenu));
  });
});
