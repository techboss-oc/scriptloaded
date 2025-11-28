<?php
if (defined('SCRIPTLOADED_PRELOADER_EMITTED')) {
    return;
}
define('SCRIPTLOADED_PRELOADER_EMITTED', true);
?>
<style data-scriptloaded-preloader>
  html.preloader-active,
  html.preloader-active body {
    overflow: hidden;
  }
  .scriptloaded-preloader {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at top, rgba(32, 76, 255, 0.2), rgba(4, 10, 20, 0.95));
    backdrop-filter: blur(18px);
    transition: opacity 0.5s ease, visibility 0.5s ease;
  }
  .scriptloaded-preloader.is-hidden {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
  }
  .scriptloaded-preloader__shell {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
    text-align: center;
  }
  .scriptloaded-preloader__orb {
    position: relative;
    width: 82px;
    height: 82px;
    border-radius: 9999px;
    background: conic-gradient(from 0deg, #1A73E8, #7C4DFF, #1A73E8);
    animation: scriptloaded-spin 1.8s linear infinite;
    box-shadow: 0 0 40px rgba(26, 115, 232, 0.45);
  }
  .scriptloaded-preloader__orb::after {
    content: '';
    position: absolute;
    inset: 18px;
    border-radius: 9999px;
    background: #0b1523;
  }
  .scriptloaded-preloader__ring {
    position: absolute;
    inset: -12px;
    border-radius: 9999px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    animation: scriptloaded-pulse 2.2s ease-in-out infinite;
  }
  .scriptloaded-preloader__label {
    font-size: 0.85rem;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.7);
  }
  .scriptloaded-preloader__note {
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.85);
    max-width: 18rem;
  }
  @media (prefers-reduced-motion: reduce) {
    .scriptloaded-preloader__orb,
    .scriptloaded-preloader__ring {
      animation: none;
    }
  }
  @keyframes scriptloaded-spin {
    to { transform: rotate(360deg); }
  }
  @keyframes scriptloaded-pulse {
    0% { opacity: 0.2; transform: scale(0.9); }
    50% { opacity: 0.5; transform: scale(1); }
    100% { opacity: 0.2; transform: scale(1.05); }
  }
</style>
<div class="scriptloaded-preloader" data-preloader>
  <div class="scriptloaded-preloader__shell">
    <div class="scriptloaded-preloader__orb">
      <span class="scriptloaded-preloader__ring"></span>
    </div>
    <p class="scriptloaded-preloader__label">Loading</p>
    <p class="scriptloaded-preloader__note">Preparing your Scriptloaded experience…</p>
  </div>
</div>
<script>
  (function () {
    const preloader = document.querySelector('[data-preloader]');
    if (!preloader) {
      return;
    }
    document.documentElement.classList.add('preloader-active');
    const finish = () => {
      if (preloader.classList.contains('is-hidden')) {
        return;
      }
      preloader.classList.add('is-hidden');
      document.documentElement.classList.remove('preloader-active');
      setTimeout(() => {
        preloader.remove();
      }, 600);
    };
    window.addEventListener('load', finish);
    setTimeout(finish, 6000);
  })();
</script>
