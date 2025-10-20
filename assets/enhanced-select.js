(function () {
  'use strict';

  const SELECTOR = 'select[data-enhanced-select]';
  const OPEN_CLASS = 'is-open';
  const instances = new WeakMap();
  let activeInstance = null;
  let idCounter = 0;

  function closeInstance(instance, focusTrigger) {
    if (!instance) {
      return;
    }

    const { wrapper, trigger, menu, setActiveOption } = instance;
    wrapper.classList.remove(OPEN_CLASS);
    trigger.setAttribute('aria-expanded', 'false');
    menu.removeAttribute('aria-activedescendant');
    setActiveOption(null);
    if (focusTrigger) {
      trigger.focus();
    }
    if (activeInstance === instance) {
      activeInstance = null;
    }
  }

  function handleDocumentClick(event) {
    if (!activeInstance) {
      return;
    }

    if (!activeInstance.wrapper.contains(event.target)) {
      closeInstance(activeInstance, false);
    }
  }

  function handleDocumentKeydown(event) {
    if (!activeInstance) {
      return;
    }

    if (event.key === 'Escape') {
      event.preventDefault();
      closeInstance(activeInstance, true);
    }
  }

  const DEFAULT_LINE_HEIGHT = 20;

  function normalizeWheelDelta(event, element) {
    let deltaY = event.deltaY || 0;

    if (!Number.isFinite(deltaY)) {
      return 0;
    }

    if (event.deltaMode === 1) {
      const lineHeight = element ? parseFloat(window.getComputedStyle(element).lineHeight || '') : NaN;
      const fallback = Number.isNaN(lineHeight) || lineHeight <= 0 ? DEFAULT_LINE_HEIGHT : lineHeight;
      deltaY *= fallback;
    } else if (event.deltaMode === 2) {
      const height = element ? element.clientHeight : window.innerHeight || DEFAULT_LINE_HEIGHT;
      deltaY *= height;
    }

    return deltaY;
  }

  function initSelect(select) {
    if (instances.has(select)) {
      return;
    }

    const wrapper = document.createElement('div');
    wrapper.className = 'enhanced-select';

    const parent = select.parentNode;
    if (!parent) {
      return;
    }

    const selectId = select.id || `enhanced-select-${++idCounter}`;
    if (!select.id) {
      select.id = selectId;
    }

    const labelElement = Array.from(document.querySelectorAll('label')).find((label) => label.htmlFor === selectId) || null;

    parent.insertBefore(wrapper, select);
    wrapper.appendChild(select);

    select.setAttribute('data-enhanced-select-active', 'true');
    select.tabIndex = -1;

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'enhanced-select__trigger';
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');
    trigger.setAttribute('aria-controls', `${selectId}-listbox`);

    const valueSpan = document.createElement('span');
    valueSpan.className = 'enhanced-select__value';
    trigger.appendChild(valueSpan);

    wrapper.appendChild(trigger);

    const menu = document.createElement('ul');
    menu.className = 'enhanced-select__menu';
    menu.id = `${selectId}-listbox`;
    menu.setAttribute('role', 'listbox');
    menu.tabIndex = -1;

    if (labelElement) {
      if (!labelElement.id) {
        labelElement.id = `${selectId}-label`;
      }
      trigger.setAttribute('aria-labelledby', labelElement.id);
      menu.setAttribute('aria-labelledby', labelElement.id);
    } else if (select.hasAttribute('aria-label')) {
      const ariaLabel = select.getAttribute('aria-label');
      if (ariaLabel) {
        trigger.setAttribute('aria-label', ariaLabel);
        menu.setAttribute('aria-label', ariaLabel);
      }
    }

    wrapper.appendChild(menu);

    const options = Array.from(select.options).map((option, index) => {
      const item = document.createElement('li');
      item.className = 'enhanced-select__option';
      item.setAttribute('role', 'option');
      const optionId = `${selectId}-option-${index}`;
      item.id = optionId;
      item.dataset.value = option.value;
      item.textContent = option.textContent;
      if (option.dir) {
        item.dir = option.dir;
      }
      if (option.lang) {
        item.lang = option.lang;
      }
      if (option.disabled) {
        item.setAttribute('aria-disabled', 'true');
        item.classList.add('is-disabled');
      }
      menu.appendChild(item);
      return item;
    });

    if (!select.value && options.length > 0) {
      const firstEnabled = options.find((item) => !item.classList.contains('is-disabled'));
      if (firstEnabled) {
        select.value = firstEnabled.dataset.value;
      }
    }

    let currentActive = null;

    function setSelected(optionElement, dispatchChange) {
      if (!optionElement || optionElement.classList.contains('is-disabled')) {
        return;
      }

      options.forEach((item) => {
        item.classList.remove('is-selected');
        item.setAttribute('aria-selected', 'false');
      });

      optionElement.classList.add('is-selected');
      optionElement.setAttribute('aria-selected', 'true');
      valueSpan.textContent = optionElement.textContent;

      const newValue = optionElement.dataset.value;
      if (select.value !== newValue) {
        select.value = newValue;
      }

      Array.from(select.options).forEach((opt) => {
        opt.selected = opt.value === newValue;
      });

      if (dispatchChange) {
        const changeEvent = new Event('change', { bubbles: true });
        select.dispatchEvent(changeEvent);
      }
    }

    function setActiveOption(optionElement) {
      if (currentActive === optionElement) {
        return;
      }

      if (currentActive) {
        currentActive.classList.remove('is-active');
      }

      currentActive = optionElement || null;

      if (currentActive) {
        currentActive.classList.add('is-active');
        menu.setAttribute('aria-activedescendant', currentActive.id);
        currentActive.scrollIntoView({ block: 'nearest' });
      } else {
        menu.removeAttribute('aria-activedescendant');
      }
    }

    function openMenu() {
      if (activeInstance && activeInstance !== instance) {
        closeInstance(activeInstance, false);
      }

      wrapper.classList.add(OPEN_CLASS);
      trigger.setAttribute('aria-expanded', 'true');
      try {
        menu.focus({ preventScroll: true });
      } catch (error) {
        menu.focus();
      }
      activeInstance = instance;

      const selected = options.find((item) => item.classList.contains('is-selected'));
      setActiveOption(selected || options.find((item) => !item.classList.contains('is-disabled')) || null);
    }

    function closeMenu(focusTrigger) {
      closeInstance(instance, focusTrigger);
    }

    function moveActive(step) {
      if (!options.length) {
        return;
      }

      const enabledOptions = options.filter((item) => !item.classList.contains('is-disabled'));
      if (!enabledOptions.length) {
        return;
      }

      const current = currentActive && !currentActive.classList.contains('is-disabled')
        ? currentActive
        : enabledOptions.find((item) => item.classList.contains('is-selected')) || enabledOptions[0];

      const currentIndex = enabledOptions.indexOf(current);
      const nextIndex = (currentIndex + step + enabledOptions.length) % enabledOptions.length;
      const nextOption = enabledOptions[nextIndex];
      setActiveOption(nextOption);
    }

    trigger.addEventListener('click', (event) => {
      event.preventDefault();
      if (wrapper.classList.contains(OPEN_CLASS)) {
        closeMenu(true);
      } else {
        openMenu();
      }
    });

    trigger.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        event.preventDefault();
        if (!wrapper.classList.contains(OPEN_CLASS)) {
          openMenu();
        }
        moveActive(event.key === 'ArrowDown' ? 1 : -1);
      } else if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        if (wrapper.classList.contains(OPEN_CLASS)) {
          const optionToSelect = currentActive || options.find((item) => item.classList.contains('is-selected'));
          if (optionToSelect) {
            setSelected(optionToSelect, true);
          }
          closeMenu(true);
        } else {
          openMenu();
        }
      }
    });

    menu.addEventListener('click', (event) => {
      const optionElement = event.target.closest('.enhanced-select__option');
      if (!optionElement || optionElement.classList.contains('is-disabled')) {
        return;
      }

      setSelected(optionElement, true);
      closeMenu(true);
    });

    menu.addEventListener('mousemove', (event) => {
      const optionElement = event.target.closest('.enhanced-select__option');
      if (!optionElement || optionElement.classList.contains('is-disabled')) {
        return;
      }
      setActiveOption(optionElement);
    });

    menu.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        event.preventDefault();
        moveActive(event.key === 'ArrowDown' ? 1 : -1);
      } else if (event.key === 'Home') {
        event.preventDefault();
        const first = options.find((item) => !item.classList.contains('is-disabled'));
        setActiveOption(first || null);
      } else if (event.key === 'End') {
        event.preventDefault();
        const enabled = options.filter((item) => !item.classList.contains('is-disabled'));
        setActiveOption(enabled.length ? enabled[enabled.length - 1] : null);
      } else if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        const optionToSelect = currentActive || options.find((item) => item.classList.contains('is-selected'));
        if (optionToSelect) {
          setSelected(optionToSelect, true);
        }
        closeMenu(true);
      } else if (event.key === 'Escape') {
        event.preventDefault();
        closeMenu(true);
      } else if (event.key === 'Tab') {
        closeMenu(false);
      }
    });

    const handleMenuWheel = (event) => {
      if (!wrapper.classList.contains(OPEN_CLASS)) {
        return;
      }

      if (!menu.contains(event.target)) {
        return;
      }

      const maxScroll = menu.scrollHeight - menu.clientHeight;
      if (maxScroll <= 0) {
        return;
      }

      const deltaY = normalizeWheelDelta(event, menu);
      if (deltaY === 0) {
        return;
      }

      const currentScroll = menu.scrollTop;
      const nextScroll = Math.max(0, Math.min(currentScroll + deltaY, maxScroll));

      const reachedBoundary = (deltaY < 0 && currentScroll <= 0) || (deltaY > 0 && currentScroll >= maxScroll);

      if (nextScroll === currentScroll && !reachedBoundary) {
        event.preventDefault();
        event.stopPropagation();

        if (typeof menu.scrollBy === 'function') {
          menu.scrollBy({ top: deltaY });
        }

        return;
      }

      event.preventDefault();
      event.stopPropagation();

      if (nextScroll !== currentScroll) {
        menu.scrollTop = nextScroll;

        if (menu.scrollTop !== nextScroll && typeof menu.scrollBy === 'function') {
          menu.scrollBy({ top: deltaY });
        }
      }
    };

    menu.addEventListener('wheel', handleMenuWheel, { passive: false });
    menu.addEventListener('mousewheel', handleMenuWheel, { passive: false });
    wrapper.addEventListener('wheel', handleMenuWheel, { passive: false });
    wrapper.addEventListener('mousewheel', handleMenuWheel, { passive: false });

    let touchStartY = null;

    menu.addEventListener('touchstart', (event) => {
      if (!wrapper.classList.contains(OPEN_CLASS)) {
        return;
      }

      if (!event.touches || event.touches.length !== 1) {
        return;
      }

      touchStartY = event.touches[0].clientY;
    }, { passive: true });

    menu.addEventListener('touchmove', (event) => {
      if (!wrapper.classList.contains(OPEN_CLASS)) {
        return;
      }

      if (!event.touches || event.touches.length !== 1) {
        return;
      }

      if (touchStartY === null) {
        touchStartY = event.touches[0].clientY;
      }

      const currentY = event.touches[0].clientY;
      const deltaY = touchStartY - currentY;

      const maxScroll = menu.scrollHeight - menu.clientHeight;
      if (maxScroll <= 0 || deltaY === 0) {
        return;
      }

      const atTop = menu.scrollTop <= 0;
      const atBottom = menu.scrollTop >= maxScroll;

      if ((deltaY < 0 && atTop) || (deltaY > 0 && atBottom)) {
        event.preventDefault();
      }

      touchStartY = currentY;
      event.stopPropagation();
    }, { passive: false });

    const handleTouchEnd = () => {
      touchStartY = null;
    };

    menu.addEventListener('touchend', handleTouchEnd);
    menu.addEventListener('touchcancel', handleTouchEnd);

    select.addEventListener('change', () => {
      const match = options.find((item) => item.dataset.value === select.value);
      if (match) {
        setSelected(match, false);
      }
    });

    options.forEach((item) => {
      if (item.dataset.value === select.value) {
        setSelected(item, false);
      }
    });

    if (!valueSpan.textContent && options.length) {
      const initial = options.find((item) => item.classList.contains('is-selected'))
        || options.find((item) => !item.classList.contains('is-disabled'));
      if (initial) {
        setSelected(initial, false);
      }
    }

    const instance = {
      wrapper,
      trigger,
      menu,
      options,
      setSelected,
      setActiveOption,
      closeMenu,
    };

    instances.set(select, instance);
  }

  function scan() {
    document.querySelectorAll(SELECTOR).forEach((select) => {
      if (!instances.has(select)) {
        initSelect(select);
      }
    });
  }

  document.addEventListener('click', handleDocumentClick);
  document.addEventListener('keydown', handleDocumentKeydown);

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', scan, { once: true });
  } else {
    scan();
  }

  const observer = new MutationObserver((mutations) => {
    if (mutations.some((mutation) => mutation.addedNodes.length || mutation.removedNodes.length)) {
      scan();
    }
  });

  observer.observe(document.documentElement, { childList: true, subtree: true });
})();
