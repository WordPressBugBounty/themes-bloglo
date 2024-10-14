//--------------------------------------------------------------------//
// Global helper functions
//--------------------------------------------------------------------//

/**
 * Matches polyfill.
 *
 * @since 1.0.0
 */
if (!Element.prototype.matches) {
  Element.prototype.matches =
    Element.prototype.msMatchesSelector ||
    Element.prototype.webkitMatchesSelector;
}

/**
 * Closest polyfill.
 *
 * @since 1.0.0
 */
if (!Element.prototype.closest) {
  Element.prototype.closest = function (s) {
    var el = this;

    do {
      if (el.matches(s)) {
        return el;
      }
      el = el.parentElement || el.parentNode;
    } while (null !== el && 1 === el.nodeType);

    return null;
  };
}

/**
 * Foreach polyfill.
 *
 * @since 1.0.0
 */
if (window.NodeList && !NodeList.prototype.forEach) {
  NodeList.prototype.forEach = Array.prototype.forEach;
}

/**
 * Element.prototype.classList for IE8/9, Safari.
 *
 * @since 1.10
 */
(function () {
  // Helpers.
  var trim = function (s) {
      return s.replace(/^\s+|\s+$/g, "");
    },
    regExp = function (name) {
      return new RegExp("(^|\\s+)" + name + "(\\s+|$)");
    },
    forEach = function (list, fn, scope) {
      for (var i = 0; i < list.length; i++) {
        fn.call(scope, list[i]);
      }
    };

  // Class list object with basic methods.
  function ClassList(element) {
    this.element = element;
  }

  ClassList.prototype = {
    add: function () {
      forEach(
        arguments,
        function (name) {
          if (!this.contains(name)) {
            this.element.className = trim(this.element.className + " " + name);
          }
        },
        this
      );
    },
    remove: function () {
      forEach(
        arguments,
        function (name) {
          this.element.className = trim(
            this.element.className.replace(regExp(name), " ")
          );
        },
        this
      );
    },
    toggle: function (name) {
      return this.contains(name)
        ? (this.remove(name), false)
        : (this.add(name), true);
    },
    contains: function (name) {
      return regExp(name).test(this.element.className);
    },
    item: function (i) {
      return this.element.className.split(/\s+/)[i] || null;
    },

    // bonus
    replace: function (oldName, newName) {
      this.remove(oldName), this.add(newName);
    },
  };

  // IE8/9, Safari
  // Remove this if statements to override native classList.
  if (!("classList" in Element.prototype)) {
    // Use this if statement to override native classList that does not have for example replace() method.
    // See browser compatibility: https://developer.mozilla.org/en-US/docs/Web/API/Element/classList#Browser_compatibility.
    // if (!('classList' in Element.prototype) ||
    //     !('classList' in Element.prototype && Element.prototype.classList.replace)) {
    Object.defineProperty(Element.prototype, "classList", {
      get: function () {
        return new ClassList(this);
      },
    });
  }

  // For others replace() support.
  if (window.DOMTokenList && !DOMTokenList.prototype.replace) {
    DOMTokenList.prototype.replace = ClassList.prototype.replace;
  }
})();

/**
 * Index polyfill.
 *
 * @since 1.0.0
 */
var blogloGetIndex = function (el) {
  var i = 0;

  while ((el = el.previousElementSibling)) {
    i++;
  }

  return i;
};

/**
 * Slide Up animation.
 *
 * @since 1.0.0
 *
 * @param  {[type]} target   Element to slide.
 * @param  {Number} duration Animation duration.
 */
var blogloSlideUp = (target, duration = 500) => {
  target.style.transitionProperty = "height, margin, padding";
  target.style.transitionDuration = duration + "ms";
  target.style.boxSizing = "border-box";
  target.style.height = target.offsetHeight + "px";
  target.offsetHeight;
  target.style.overflow = "hidden";
  target.style.height = 0;
  target.style.paddingTop = 0;
  target.style.paddingBottom = 0;
  target.style.marginTop = 0;
  target.style.marginBottom = 0;
  window.setTimeout(() => {
    target.style.display = null;
    target.style.removeProperty("height");
    target.style.removeProperty("padding-top");
    target.style.removeProperty("padding-bottom");
    target.style.removeProperty("margin-top");
    target.style.removeProperty("margin-bottom");
    target.style.removeProperty("overflow");
    target.style.removeProperty("transition-duration");
    target.style.removeProperty("transition-property");
  }, duration);
};

/**
 * Slide Down animation.
 *
 * @since 1.0.0
 *
 * @param  {[type]} target   Element to slide.
 * @param  {Number} duration Animation duration.
 */
var blogloSlideDown = (target, duration = 500) => {
  target.style.removeProperty("display");
  let display = window.getComputedStyle(target).display;

  if ("none" === display) {
    display = "block";
  }

  target.style.display = display;
  let height = target.offsetHeight;
  target.style.overflow = "hidden";
  target.style.height = 0;
  target.style.paddingTop = 0;
  target.style.paddingBottom = 0;
  target.style.marginTop = 0;
  target.style.marginBottom = 0;
  target.offsetHeight;
  target.style.boxSizing = "border-box";
  target.style.transitionProperty = "height, margin, padding";
  target.style.transitionDuration = duration + "ms";
  target.style.height = height + "px";
  target.style.removeProperty("padding-top");
  target.style.removeProperty("padding-bottom");
  target.style.removeProperty("margin-top");
  target.style.removeProperty("margin-bottom");
  window.setTimeout(() => {
    target.style.removeProperty("height");
    target.style.removeProperty("overflow");
    target.style.removeProperty("transition-duration");
    target.style.removeProperty("transition-property");
  }, duration);
};

/**
 * MoveTo - A lightweight scroll animation javascript library without any dependency.
 * Version 1.8.3 (21-07-2019 00:32)
 * Licensed under MIT
 * Copyright 2019 Hasan Aydoğdu <hsnaydd@gmail.com>
 */
var blogloScrollTo = (function () {
  /**
   * Defaults
   * @type {object}
   */
  var defaults = {
    tolerance: 0,
    duration: 800,
    easing: "easeOutQuart",
    container: window,
    callback: function callback() {},
  };

  /**
   * easeOutQuart Easing Function
   * @param  {number} t - current time
   * @param  {number} b - start value
   * @param  {number} c - change in value
   * @param  {number} d - duration
   * @return {number} - calculated value
   */

  function easeOutQuart(t, b, c, d) {
    t /= d;
    t--;
    return -c * (t * t * t * t - 1) + b;
  }

  /**
   * Merge two object
   *
   * @param  {object} obj1
   * @param  {object} obj2
   * @return {object} merged object
   */
  function mergeObject(obj1, obj2) {
    var obj3 = {};
    Object.keys(obj1).forEach(function (propertyName) {
      obj3[propertyName] = obj1[propertyName];
    });
    Object.keys(obj2).forEach(function (propertyName) {
      obj3[propertyName] = obj2[propertyName];
    });
    return obj3;
  }

  /**
   * Converts camel case to kebab case
   * @param  {string} val the value to be converted
   * @return {string} the converted value
   */
  function kebabCase(val) {
    return val.replace(/([A-Z])/g, function ($1) {
      return "-" + $1.toLowerCase();
    });
  }

  /**
   * Count a number of item scrolled top
   * @param  {Window|HTMLElement} container
   * @return {number}
   */
  function countScrollTop(container) {
    if (container instanceof HTMLElement) {
      return container.scrollTop;
    }

    return container.pageYOffset;
  }

  /**
   * blogloScrollTo Constructor
   * @param {object} options Options
   * @param {object} easeFunctions Custom ease functions
   */
  function blogloScrollTo() {
    var options =
      0 < arguments.length && arguments[0] !== undefined ? arguments[0] : {};
    var easeFunctions =
      1 < arguments.length && arguments[1] !== undefined ? arguments[1] : {};
    this.options = mergeObject(defaults, options);
    this.easeFunctions = mergeObject(
      {
        easeOutQuart: easeOutQuart,
      },
      easeFunctions
    );
  }

  /**
   * Register a dom element as trigger
   * @param  {HTMLElement} dom Dom trigger element
   * @param  {function} callback Callback function
   * @return {function|void} unregister function
   */
  blogloScrollTo.prototype.registerTrigger = function (dom, callback) {
    var _this = this;

    if (!dom) {
      return;
    }

    var href = dom.getAttribute("href") || dom.getAttribute("data-target"); // The element to be scrolled

    var target =
      href && "#" !== href
        ? document.getElementById(href.substring(1))
        : document.body;
    var options = mergeObject(
      this.options,
      _getOptionsFromTriggerDom(dom, this.options)
    );

    if ("function" === typeof callback) {
      options.callback = callback;
    }

    var listener = function listener(e) {
      e.preventDefault();

      _this.move(target, options);
    };

    dom.addEventListener("click", listener, false);
    return function () {
      return dom.removeEventListener("click", listener, false);
    };
  };

  /**
   * Move
   * Scrolls to given element by using easeOutQuart function
   * @param  {HTMLElement|number} target Target element to be scrolled or target position
   * @param  {object} options Custom options
   */
  blogloScrollTo.prototype.move = function (target) {
    var _this2 = this;

    var options =
      1 < arguments.length && arguments[1] !== undefined ? arguments[1] : {};

    if (0 !== target && !target) {
      return;
    }

    options = mergeObject(this.options, options);
    var distance =
      "number" === typeof target ? target : target.getBoundingClientRect().top;
    var from = countScrollTop(options.container);
    var startTime = null;
    var lastYOffset;
    distance -= options.tolerance; // rAF loop

    var loop = function loop(currentTime) {
      var currentYOffset = countScrollTop(_this2.options.container);

      if (!startTime) {
        // To starts time from 1, we subtracted 1 from current time
        // If time starts from 1 The first loop will not do anything,
        // because easing value will be zero
        startTime = currentTime - 1;
      }

      var timeElapsed = currentTime - startTime;

      if (lastYOffset) {
        if (
          (0 < distance && lastYOffset > currentYOffset) ||
          (0 > distance && lastYOffset < currentYOffset)
        ) {
          return options.callback(target);
        }
      }

      lastYOffset = currentYOffset;

      var val = _this2.easeFunctions[options.easing](
        timeElapsed,
        from,
        distance,
        options.duration
      );

      options.container.scroll(0, val);

      if (timeElapsed < options.duration) {
        window.requestAnimationFrame(loop);
      } else {
        options.container.scroll(0, distance + from);
        options.callback(target);
      }
    };

    window.requestAnimationFrame(loop);
  };

  /**
   * Adds custom ease function
   * @param {string}   name Ease function name
   * @param {function} fn   Ease function
   */
  blogloScrollTo.prototype.addEaseFunction = function (name, fn) {
    this.easeFunctions[name] = fn;
  };

  /**
   * Returns options which created from trigger dom element
   * @param  {HTMLElement} dom Trigger dom element
   * @param  {object} options The instance's options
   * @return {object} The options which created from trigger dom element
   */
  function _getOptionsFromTriggerDom(dom, options) {
    var domOptions = {};
    Object.keys(options).forEach(function (key) {
      var value = dom.getAttribute("data-mt-".concat(kebabCase(key)));

      if (value) {
        domOptions[key] = isNaN(value) ? value : parseInt(value, 10);
      }
    });
    return domOptions;
  }

  return blogloScrollTo;
})();

/**
 * Get all of an element's parent elements up the DOM tree
 *
 * @since 1.0.0
 *
 * @param  {Node}   elem     The element.
 * @param  {String} selector Selector to match against [optional].
 * @return {Array}           The parent elements.
 */
var blogloGetParents = (elem, selector) => {
  // Element.matches() polyfill.
  if (!Element.prototype.matches) {
    Element.prototype.matches =
      Element.prototype.matchesSelector ||
      Element.prototype.mozMatchesSelector ||
      Element.prototype.msMatchesSelector ||
      Element.prototype.oMatchesSelector ||
      Element.prototype.webkitMatchesSelector ||
      function (s) {
        var matches = (this.document || this.ownerDocument).querySelectorAll(s),
          i = matches.length;
        while (0 <= --i && matches.item(i) !== this) {}
        return -1 < i;
      };
  }

  // Setup parents array.
  var parents = [];

  // Get matching parent elements.
  for (; elem && elem !== document; elem = elem.parentNode) {
    // Add matching parents to array.
    if (selector) {
      if (elem.matches(selector)) {
        parents.push(elem);
      }
    } else {
      parents.push(elem);
    }
  }
  return parents;
};

// CustomEvent() constructor functionality in Internet Explorer 9 and higher.
(function () {
  if ("function" === typeof window.CustomEvent) {
    return false;
  }

  function CustomEvent(event, params) {
    params = params || { bubbles: false, cancelable: false, detail: undefined };
    var evt = document.createEvent("CustomEvent");
    evt.initCustomEvent(
      event,
      params.bubbles,
      params.cancelable,
      params.detail
    );
    return evt;
  }

  CustomEvent.prototype = window.Event.prototype;
  window.CustomEvent = CustomEvent;
})();

/**
 * Trigger custom JS Event.
 *
 * @since 1.0.0
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent
 * @param {Node} el Dom Node element on which the event is to be triggered.
 * @param {Node} typeArg A DOMString representing the name of the event.
 * @param {String} A CustomEventInit dictionary, having the following fields:
 *			"detail", optional and defaulting to null, of type any, that is an event-dependent value associated with the event.
 */
var blogloTriggerEvent = function (el, typeArg) {
  var customEventInit =
    2 < arguments.length && arguments[2] !== undefined ? arguments[2] : {};

  var event = new CustomEvent(typeArg, customEventInit);
  el.dispatchEvent(event);
};

// Main
(function () {
  //--------------------------------------------------------------------//
  // Variable caching
  //--------------------------------------------------------------------//

  var blogloScrollButton = document.querySelector("#bloglo-scroll-top");
  var pageWrapper = document.getElementById("page");

  //--------------------------------------------------------------------//
  // Local helper functions
  //--------------------------------------------------------------------//

  /**
   * Submenu overflow helper
   *
   * @since 1.0.0
   */
  var blogloSmartSubmenus = () => {
    if (document.body.classList.contains("bloglo-is-mobile")) {
      return;
    }

    var el, elPosRight, elPosLeft, winRight;

    winRight = window.innerWidth;

    document.querySelectorAll(".sub-menu").forEach((item) => {
      // Set item to be visible so we can grab offsets
      item.style.visibility = "visible";

      // Left offset
      const rect = item.getBoundingClientRect();
      elPosLeft = rect.left + window.pageXOffset;

      // Right offset
      elPosRight = elPosLeft + rect.width;

      // Remove styles
      item.removeAttribute("style");

      // Decide where to open
      if (elPosRight > winRight) {
        item.closest("li").classList.add("opens-left");
      } else if (0 > elPosLeft) {
        item.closest("li").classList.add("opens-right");
      }
    });
  };

  /**
   * Debounce functions for better performance
   * (c) 2018 Chris Ferdinandi, MIT License, https://gomakethings.com
   *
   * @since 1.0.0
   *
   * @param  {Function} fn The function to debounce
   */
  var blogloDebounce = (fn) => {
    // Setup a timer
    var timeout;

    // Return a function to run debounced
    return function () {
      // Setup the arguments
      var context = this;
      var args = arguments;

      // If there's a timer, cancel it
      if (timeout) {
        window.cancelAnimationFrame(timeout);
      }

      // Setup the new requestAnimationFrame()
      timeout = window.requestAnimationFrame(function () {
        fn.apply(this, args);
      });
    };
  };

  /**
   * Handles Scroll to Top button click
   *
   * @since 1.0.0
   */
  var blogloScrollTopButton = () => {
    if (null === blogloScrollButton) {
      return;
    }

    if (450 < window.pageYOffset || 450 < document.documentElement.scrollTop) {
      blogloScrollButton.classList.add("bloglo-visible");
    } else {
      blogloScrollButton.classList.remove("bloglo-visible");
    }
  };

  /**
   * Handles Sticky Header functionality.
   *
   * @since 1.0.0
   */
  var blogloStickyHeader = () => {
    // Check if sticky is enabled.
    if (!bloglo_vars["sticky-header"].enabled) {
      return;
    }

    var header = document.getElementById("bloglo-header");
    var headerInner = document.getElementById("bloglo-header-inner");
    var wpadminbar = document.getElementById("wpadminbar");

    // Check for header layout 3 & 4.
    if (document.body.classList.contains("bloglo-header-layout-3") || document.body.classList.contains("bloglo-header-layout-4")) {
      header = document.querySelector("#bloglo-header .bloglo-nav-container");
      headerInner = document.querySelector(
        "#bloglo-header .bloglo-nav-container .bloglo-container"
      );
    }

    // Mobile nav active.
    if (window.outerWidth <= bloglo_vars["responsive-breakpoint"]) {
      var header = document.getElementById("bloglo-header");
      var headerInner = document.getElementById("bloglo-header-inner");
    }

    // Check if elements exist.
    if (null === header || null === headerInner) {
      return;
    }

    // Calculate the initial sticky position.
    var stickyPosition = header.getBoundingClientRect().bottom;
    var sticky = 0 >= stickyPosition - tolerance;
    var tolerance;
    var stickyPlaceholder;

    // Check if there is a top bar.
    if (null === wpadminbar) {
      tolerance = 0;
    } else if (600 >= window.outerWidth) {
      tolerance = 0;
    } else {
      tolerance = wpadminbar.getBoundingClientRect().height;
    }

    var checkPosition = function () {
      if (null === wpadminbar) {
        tolerance = 0;
      } else if (600 >= window.outerWidth) {
        tolerance = 0;
      } else {
        tolerance = wpadminbar.getBoundingClientRect().height;
      }

      stickyPosition = header.getBoundingClientRect().bottom;
      sticky = 0 >= stickyPosition - tolerance;

      maybeStickHeader();
    };

    var maybeStickHeader = function () {
      let hideOn = bloglo_vars["sticky-header"]["hide_on"];

      // Desktop.
      if (hideOn.includes("desktop") && 992 <= window.innerWidth) {
        sticky = false;
      }

      // Tablet.
      if (
        hideOn.includes("tablet") &&
        481 <= window.innerWidth &&
        992 > window.innerWidth
      ) {
        sticky = false;
      }

      // Mobile.
      if (hideOn.includes("mobile") && 481 > window.innerWidth) {
        sticky = false;
      }

      if (sticky) {
        if (!document.body.classList.contains("bloglo-sticky-header")) {
          stickyPlaceholder = document.createElement("div");
          stickyPlaceholder.setAttribute("id", "bloglo-sticky-placeholder");

          stickyPlaceholder.style.height =
            headerInner.getBoundingClientRect().height + "px";
          header.appendChild(stickyPlaceholder);

          document.body.classList.add("bloglo-sticky-header");

          // Add sticky header offset variable.
          document.body.style.setProperty(
            "--bloglo-sticky-h-offset",
            header.offsetHeight + 20 + "px"
          );
        }
      } else {
        if (document.body.classList.contains("bloglo-sticky-header")) {
          document.body.classList.remove("bloglo-sticky-header");
          document.getElementById("bloglo-sticky-placeholder").remove();
        }

        // Remove sticky header offset variable.
        document.body.style.removeProperty("--bloglo-sticky-h-offset");
      }
    };

    // Debounce scroll.
    if ("true" !== header.getAttribute("data-scroll-listener")) {
      window.addEventListener("scroll", function () {
        blogloDebounce(checkPosition());
      });

      header.setAttribute("data-scroll-listener", "true");
    }

    // Debounce resize.
    if ("true" !== header.getAttribute("data-resize-listener")) {
      window.addEventListener("resize", function () {
        blogloDebounce(checkPosition());
      });

      header.setAttribute("data-resize-listener", "true");
    }

    // Trigger scroll.
    blogloTriggerEvent(window, "scroll");
  };

  /**
   * Handles smooth scrolling of elements that have 'bloglo-smooth-scroll' class.
   *
   * @since 1.0.0
   */
  var blogloSmoothScroll = () => {
    const scrollTo = new blogloScrollTo({
      tolerance:
        null === document.getElementById("wpadminbar")
          ? 0
          : document.getElementById("wpadminbar").getBoundingClientRect()
              .height,
    });

    const scrollTriggers = document.getElementsByClassName(
      "bloglo-smooth-scroll"
    );

    for (var i = 0; i < scrollTriggers.length; i++) {
      scrollTo.registerTrigger(scrollTriggers[i]);
    }
  };

  /**
   * Menu accessibility.
   *
   * @since 1.0.0
   */
  var blogloMenuAccessibility = () => {
    if (!document.body.classList.contains("bloglo-menu-accessibility")) {
      return;
    }

    document.querySelectorAll(".bloglo-nav").forEach((menu) => {
      // aria-haspopup
      menu.querySelectorAll("ul").forEach((subMenu) => {
        subMenu.parentNode.setAttribute("aria-haspopup", "true");
      });

      // Dropdown visibility on focus
      menu.querySelectorAll("a").forEach((link) => {
        link.addEventListener("focus", blogloMenuFocus, true);
        link.addEventListener("blur", blogloMenuFocus, true);
      });
    });
  };

  /**
   * Helper function that toggles .hovered on focused/blurred menu items.
   *
   * @since 1.0.0
   */
  function blogloMenuFocus() {
    var self = this;

    // Move up until we find .bloglo-nav
    while (!self.classList.contains("bloglo-nav")) {
      if ("li" === self.tagName.toLowerCase()) {
        if (!self.classList.contains("hovered")) {
          self.classList.add("hovered");
        } else {
          self.classList.remove("hovered");
        }
      }

      self = self.parentElement;
    }
  }

  /**
   * Helps with accessibility for keyboard only users.
   *
   * @since 1.0.0
   */
  var blogloKeyboardFocus = () => {
    document.body.addEventListener("keydown", function (e) {
      document.body.classList.add("using-keyboard");
    });

    document.body.addEventListener("mousedown", function (e) {
      document.body.classList.remove("using-keyboard");
    });
  };

  /**
   * Calculates screen width without scrollbars.
   *
   * @since 1.0.0
   */
  var blogloCalcScreenWidth = () => {
    document.body.style.setProperty(
      "--bloglo-screen-width",
      document.body.clientWidth + "px"
    );
  };

  /**
   * Adds visibility delay on navigation submenus.
   *
   * @since 1.0.0
   */
  var blogloDropdownDelay = () => {
    var hoverTimer = null;

    document
      .querySelectorAll(".bloglo-nav .menu-item-has-children")
      .forEach((item) => {
        item.addEventListener("mouseenter", function () {
          document
            .querySelectorAll(".menu-item-has-children")
            .forEach((subitem) => {
              subitem.classList.remove("hovered");
            });
        });
      });

    document
      .querySelectorAll(".bloglo-nav .menu-item-has-children")
      .forEach((item) => {
        item.addEventListener("mouseleave", function () {
          item.classList.add("hovered");

          if (null !== hoverTimer) {
            clearTimeout(hoverTimer);
            hoverTimer = null;
          }

          hoverTimer = setTimeout(() => {
            item.classList.remove("hovered");

            item
              .querySelectorAll(".menu-item-has-children")
              .forEach((childItem) => {
                childItem.classList.remove("hovered");
              });
          }, 700);
        });
      });
  };

  /**
   * Adds visibility delay for cart widget dropdown.
   *
   * @since 1.0.0
   */
  var blogloCartDropdownDelay = () => {
    var hoverTimer = null;

    document
      .querySelectorAll(".bloglo-header-widget__cart .bloglo-widget-wrapper")
      .forEach((item) => {
        item.addEventListener("mouseenter", function () {
          item.classList.remove("dropdown-visible");
        });
      });

    document
      .querySelectorAll(".bloglo-header-widget__cart .bloglo-widget-wrapper")
      .forEach((item) => {
        item.addEventListener("mouseleave", function () {
          item.classList.add("dropdown-visible");

          if (null !== hoverTimer) {
            clearTimeout(hoverTimer);
            hoverTimer = null;
          }

          hoverTimer = setTimeout(() => {
            item.classList.remove("dropdown-visible");
          }, 700);
        });
      });
  };

  /**
   * Handles header search widget functionality.
   *
   * @since 1.0.0
   */
  var blogloHeaderSearch = () => {
    var searchButton = document.querySelectorAll(".bloglo-search");

    if (0 === searchButton.length) {
      return;
    }

    searchButton.forEach((item) => {
      item.addEventListener("click", (e) => {
        e.preventDefault();

        if (item.classList.contains("bloglo-active")) {
          close_search(item);
        } else {
          show_search(item);
        }
      });
    });

    document.querySelectorAll(".bloglo-search-close").forEach((item) =>
      item.addEventListener("click", function (e) {
        e.preventDefault();
        if (!item.classList.contains("bloglo-active")) {
          close_search(document.querySelector(".bloglo-search"));
          document.querySelector(".bloglo-search").focus();
        }
      })
    );

    // Show search.
    var show_search = function (item) {
      var bloglosearch = document.querySelectorAll(".bloglo-search-form");
      bloglosearch.forEach(function (bloglosearch) {
        if (!bloglosearch) {
          return false;
        }
        document.addEventListener("keydown", function (e) {
          var firstEl, selectors, elements, lastEl, tabKey, shiftKey;
          selectors = "input, a, button";
          elements = bloglosearch.querySelectorAll(selectors);
          lastEl = elements[elements.length - 1];
          firstEl = elements[0];
          tabKey = 9 === e.keyCode;
          shiftKey = e.shiftKey;
          if (!shiftKey && tabKey && lastEl === document.activeElement) {
            e.preventDefault();
            firstEl.focus();
          }
          if (shiftKey && tabKey && firstEl === document.activeElement) {
            e.preventDefault();
            lastEl.focus();
          }
        });
      });

      // Make search visible
      document.body.classList.add("bloglo-search-visible");

      setTimeout(function () {
        // Highlight the search icon
        item.classList.add("bloglo-active");

        // Focus the input
        if (
          null !== item.nextElementSibling &&
          null !== item.nextElementSibling.querySelector("input")
        ) {
          item.nextElementSibling.querySelector("input").focus();
          item.nextElementSibling.querySelector("input").select();
        }
      }, 100);

      // Attach the ESC listener
      document.addEventListener("keydown", esc_close_search);

      // Attach the outside click listener
      pageWrapper.addEventListener("click", outside_close_search);
    };

    // Close search
    var close_search = function (item) {
      // Animate out
      document.body.classList.remove("bloglo-search-visible");

      // Unhighlight the search icon
      item.classList.remove("bloglo-active");

      // Unhook the ESC listener
      document.removeEventListener("keydown", esc_close_search);

      // Unhook the click listener
      pageWrapper.removeEventListener("click", outside_close_search);
    };

    // Esc support to close search
    var esc_close_search = function (e) {
      if (27 == e.keyCode) {
        document.querySelectorAll(".bloglo-search").forEach((item) => {
          close_search(item);
        });
      }
    };

    // Close search when clicked anywhere outside the search box
    var outside_close_search = function (e) {
      if (
        null === e.target.closest(".bloglo-search-container") &&
        null === e.target.closest(".bloglo-search")
      ) {
        document.querySelectorAll(".bloglo-search").forEach((item) => {
          close_search(item);
        });
      }
    };
  };

  /**
   * Handles mobile menu functionality.
   *
   * @since 1.0.0
   */
  var blogloMobileMenu = () => {
    var page = pageWrapper,
      nav = document.querySelector("#bloglo-header-inner .bloglo-nav"),
      current;

    document.querySelectorAll(".bloglo-mobile-nav > button").forEach((item) => {
      item.addEventListener(
        "click",
        function (e) {
          e.preventDefault();

          if (
            document.body.parentNode.classList.contains("is-mobile-menu-active")
          ) {
            close_menu();
          } else {
            show_menu();
          }
        },
        false
      );
    });

    document.addEventListener("keydown", function (e) {
      var firstEl,
        menuWrap,
        selectors,
        elements,
        lastEl,
        tabKey,
        shiftKey,
        closeIcon;
      (menuWrap = document.querySelector(".bloglo-mobile-nav")),
        (closeIcon = document.querySelector(
          ".bloglo-hamburger-bloglo-primary-nav"
        ));
      selectors =
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
      elements = menuWrap.querySelectorAll(selectors);
      lastEl = elements[elements.length - 1];
      firstEl = closeIcon;
      tabKey = 9 === e.keyCode;
      shiftKey = e.shiftKey;

      let isTabPressed = e.key === "Tab" || e.keyCode === 9;

      if (
        document.body.parentNode.classList.contains("is-mobile-menu-active")
      ) {
        if (!isTabPressed) {
          return;
        }

        if (shiftKey) {
          // if shift key pressed for shift + tab combination
          if (document.activeElement === firstEl) {
            lastEl.focus(); // add focus for the last focusable element
            e.preventDefault();
          }
        } else {
          // if tab key is pressed
          if (document.activeElement === lastEl) {
            // if focused has reached to last focusable element then focus first focusable element after pressing tab
            firstEl.focus(); // add focus for the first focusable element
            e.preventDefault();
          }
        }
      }
    });

    // Helper functions.
    var show_menu = function (e) {
      var bloglonav = document.querySelectorAll(".site-header");
      if (document.body.classList.contains("bloglo-is-mobile")) {
        bloglonav.forEach(function (bloglonav) {
          if (!bloglonav) {
            return false;
          }
        });
      }

      // Add the active class.
      document.body.parentNode.classList.add("is-mobile-menu-active");

      // Hook the ESC listener
      document.addEventListener("keyup", esc_close_menu);

      // Hook the click listener
      if (null !== page) {
        page.addEventListener("click", outside_close_menu);
      }

      // Hook the click listener for submenu toggle.
      document
        .querySelectorAll("#bloglo-header .bloglo-nav")
        .forEach((item) => {
          item.addEventListener("click", submenu_toggle);
        });

      // Slide down the menu.
      blogloSlideDown(nav, 350);
    };

    var close_menu = function (e) {
      // Remove the active class.
      document.body.parentNode.classList.remove("is-mobile-menu-active");

      // Unhook the ESC listener
      document.removeEventListener("keyup", esc_close_menu);

      // Unhook the click listener
      if (null !== page) {
        page.removeEventListener("click", outside_close_menu);
      }

      // Close submenus
      document
        .querySelectorAll("#bloglo-header .bloglo-nav > ul > .bloglo-open")
        .forEach((item) => {
          submenu_display_toggle(item);
        });

      nav.querySelectorAll(".hovered").forEach((li) => {
        li.classList.remove("hovered");
      });

      if (document.body.classList.contains("bloglo-is-mobile")) {
        // Unhook the click listener for submenu toggle
        document
          .querySelectorAll("#bloglo-header .bloglo-nav")
          .forEach((item) => {
            item.removeEventListener("click", submenu_toggle);
          });

        // Slide up the menu
        blogloSlideUp(nav, 250);
      } else {
        nav.style.display = null;
      }
    };

    var outside_close_menu = function (e) {
      if (
        null === e.target.closest(".bloglo-hamburger") &&
        null === e.target.closest(".site-navigation")
      ) {
        close_menu();
      }
    };

    var esc_close_menu = function (e) {
      if (27 == e.keyCode) {
        close_menu();
      }
    };

    var submenu_toggle = function (e) {
      if (e.target.parentElement.querySelectorAll(".sub-menu").length) {
        e.preventDefault();
        if ("button" === e.target.type) {
          submenu_display_toggle(e.target.parentElement);
        }
      }
    };

    // Show or hide the sub menu.
    var submenu_display_toggle = (current) => {
      if (current.classList.contains("bloglo-open")) {
        current.classList.remove("bloglo-open");
        current.querySelectorAll(".sub-menu").forEach((submenu) => {
          //submenu.style.display = null;
          blogloSlideUp(submenu, 350);
        });

        // Close all submenus automatically.
        current.querySelectorAll("li").forEach((item) => {
          item.classList.remove("bloglo-open");
          item.querySelectorAll(".sub-menu").forEach((submenu) => {
            //submenu.style.display = null;
            blogloSlideUp(submenu, 350);
          });
        });
      } else {
        current.querySelectorAll(".sub-menu").forEach((submenu) => {
          // Target first level elements only.
          if (current === submenu.parentElement) {
            //submenu.style.display = 'block';
            blogloSlideDown(submenu, 350);
          }
        });

        current.classList.add("bloglo-open");
      }
    };

    // Create custom event for closing mobile menu.
    document.addEventListener("bloglo-close-mobile-menu", close_menu);
  };

  /**
   * Bloglo preloader.
   *
   * @since 1.0.0
   */
  var blogloPreloader = (timeout = 0) => {
    var preloader = document.getElementById("bloglo-preloader");

    if (null === preloader) {
      return;
    }

    var delay = 250;

    var hide_preloader = () => {
      if (document.body.classList.contains("bloglo-loaded")) {
        return;
      }

      // Start fade out animation.
      document.body.classList.add("bloglo-loading");

      setTimeout(function () {
        // Fade out animation completed - set display none
        document.body.classList.replace("bloglo-loading", "bloglo-loaded");

        // Dispatch event when preloader is done
        blogloTriggerEvent(document.body, "bloglo-preloader-done");
      }, delay);
    };

    // Set timeout or hide immediately
    if (0 < timeout) {
      setTimeout(function () {
        hide_preloader();
      }, timeout);
    } else {
      hide_preloader();
    }

    return false;
  };

  /**
   * Handles comments toggle functionality.
   *
   * @since 1.0.0
   */
  var blogloToggleComments = () => {
    if (!document.body.classList.contains("bloglo-has-comments-toggle")) {
      return;
    }

    if (null == document.getElementById("bloglo-comments-toggle")) {
      return;
    }

    var toggleComments = (e) => {
      if ("undefined" !== typeof e) {
        e.preventDefault();
      }

      if (document.body.classList.contains("comments-visible")) {
        document.body.classList.remove("comments-visible");
        document
          .getElementById("bloglo-comments-toggle")
          .querySelector("span").innerHTML =
          bloglo_vars.strings.comments_toggle_show;
      } else {
        document.body.classList.add("comments-visible");
        document
          .getElementById("bloglo-comments-toggle")
          .querySelector("span").innerHTML =
          bloglo_vars.strings.comments_toggle_hide;
      }
    };

    if (
      null !== document.getElementById("bloglo-comments-toggle") &&
      (-1 !== location.href.indexOf("#comment") ||
        -1 !== location.href.indexOf("respond"))
    ) {
      toggleComments();
    }

    document
      .getElementById("bloglo-comments-toggle")
      .addEventListener("click", toggleComments);
  };

  /**
   * Handles toggling and smooth scrolling when clicked on "Comments" link
   *
   * @since 1.0.0
   */
  var blogloCommentsClick = () => {
    var commentsLink = document.querySelector(".single .comments-link");

    if (null === commentsLink) {
      return;
    }

    commentsLink.addEventListener("click", function (e) {
      // Show comments if hidden under a toggle
      if (
        document.body.classList.contains("bloglo-has-comments-toggle") &&
        !document.body.classList.contains("comments-visible")
      ) {
        document.getElementById("bloglo-comments-toggle").click();
      }
    });
  };

  /**
   * Removes inline styles on menus on resize.
   *
   * @since 1.0.0
   */
  var blogloCheckMobileMenu = () => {
    // Update body class if mobile breakpoint is reached.
    if (window.innerWidth <= bloglo_vars["responsive-breakpoint"]) {
      document.body.classList.add("bloglo-is-mobile");
    } else {
      if (document.body.classList.contains("bloglo-is-mobile")) {
        document.body.classList.remove("bloglo-is-mobile");
        blogloTriggerEvent(document, "bloglo-close-mobile-menu");
      }
    }
  };

  //--------------------------------------------------------------------//
  // Events
  //--------------------------------------------------------------------//

  // DOM ready
  document.addEventListener("DOMContentLoaded", function () {
    blogloPreloader(5000);
    blogloMenuAccessibility();
    blogloKeyboardFocus();
    blogloScrollTopButton();
    blogloSmoothScroll();
    blogloDropdownDelay();
    blogloToggleComments();
    blogloHeaderSearch();
    blogloMobileMenu();
    blogloCheckMobileMenu();
    blogloSmartSubmenus();
    blogloCommentsClick();
    blogloCartDropdownDelay();
    blogloStickyHeader();
    blogloCalcScreenWidth();
  });

  // Window load
  window.addEventListener("load", function () {
    blogloPreloader();
  });

  // Scroll
  window.addEventListener("scroll", function () {
    blogloDebounce(blogloScrollTopButton());
  });

  // Resize
  window.addEventListener("resize", function () {
    blogloDebounce(blogloSmartSubmenus());
    blogloDebounce(blogloCheckMobileMenu());
    blogloDebounce(blogloCalcScreenWidth());
  });

  // Bloglo ready
  blogloTriggerEvent(document.body, "bloglo-ready");

  //--------------------------------------------------------------------//
  // Global
  //--------------------------------------------------------------------//

  window.bloglo = window.bloglo || {};

  // Make these function global.
  window.bloglo.preloader = blogloPreloader;
  window.bloglo.stickyHeader = blogloStickyHeader;

  window.App = {};
  App.config = {
    headroom: {
      enabled: true,
      options: {
        classes: {
          initial: "headroom",
          pinned: "is-pinned",
          unpinned: "is-unpinned",
          top: "is-top",
          notTop: "is-not-top",
          bottom: "is-bottom",
          notBottom: "is-not-bottom",
          frozen: "is-frozen",
        },
      },
    },
    ajax: {
      enabled: true,
    },
    cursorFollower: {
      enabled: true,
      disableBreakpoint: "992",
    },
  };
  App.html = document.querySelector("html");
  App.body = document.querySelector("body");
  window.onload = function () {
    if (App.config.cursorFollower.enabled) {
      Cursor.init();
    }
  };
  const Cursor = (function () {
    const cursor = document.querySelector(".bloglo-js-cursor");
    let follower;
    let label;
    let icon;

    let clientX;
    let clientY;
    let cursorWidth;
    let cursorHeight;
    let cursorTriggers;
    let state;

    function variables() {
      follower = cursor.querySelector(".bloglo-js-follower");
      label = cursor.querySelector(".bloglo-js-label");
      icon = cursor.querySelector(".bloglo-js-icon");

      clientX = -100;
      clientY = -100;
      cursorWidth = cursor.offsetWidth / 2;
      cursorHeight = cursor.offsetHeight / 2;
      cursorTriggers;
      state = false;
    }

    function init() {
      if (!cursor) {
        return;
      }

      variables();
      state = true;
      cursor.classList.add("is-enabled");

      document.addEventListener("mousedown", (e) => {
        cursor.classList.add("is-mouse-down");
      });

      document.addEventListener("mouseup", (e) => {
        cursor.classList.remove("is-mouse-down");
      });

      document.addEventListener("mousemove", (event) => {
        clientX = event.clientX;
        clientY = event.clientY;
      });

      const render = () => {
        cursor.style.transform = `translate(${clientX - cursorWidth}px, ${
          clientY - cursorHeight
        }px)`;
        requestAnimationFrame(render);
      };

      requestAnimationFrame(render);

      update();
      breakpoint();
    }

    function enterHandler({ target }) {
      cursor.classList.add("is-active");

      if (target.getAttribute("data-cursor-label")) {
        App.body.classList.add("is-cursor-active");
        cursor.classList.add("has-label");
        label.innerHTML = target.getAttribute("data-cursor-label");
      }

      if (target.getAttribute("data-cursor-icon")) {
        App.body.classList.add("is-cursor-active");
        cursor.classList.add("has-icon");
        const iconAttr = target.getAttribute("data-cursor-icon");
      }
    }

    function leaveHandler({ target }) {
      App.body.classList.remove("is-cursor-active");
      cursor.classList.remove("is-active");
      cursor.classList.remove("has-label");
      cursor.classList.remove("has-icon");
      label.innerHTML = "";
      icon.innerHTML = "";
    }

    function update() {
      if (!cursor) {
        return;
      }

      cursorTriggers = document.querySelectorAll([
        "button",
        "a",
        "input",
        "[data-cursor]",
        "[data-cursor-label]",
        "[data-cursor-icon]",
        "textarea",
      ]);

      cursorTriggers.forEach((el) => {
        el.addEventListener("mouseenter", enterHandler);
        el.addEventListener("mouseleave", leaveHandler);
      });
    }

    function clear() {
      if (!cursor) {
        return;
      }

      cursorTriggers.forEach((el) => {
        el.removeEventListener("mouseenter", enterHandler);
        el.removeEventListener("mouseleave", leaveHandler);
      });
    }

    function hide() {
      if (!cursor) {
        return;
      }
      cursor.classList.add("is-hidden");
    }

    function show() {
      if (!cursor) {
        return;
      }
      cursor.classList.remove("is-hidden");
    }

    function breakpoint() {
      if (!state) {
        return;
      }
      if (!App.config.cursorFollower.disableBreakpoint) {
        return;
      }

      let width = 0 < window.innerWidth ? window.innerWidth : screen.width;

      if (width < App.config.cursorFollower.disableBreakpoint) {
        state = false;
        cursor.classList.remove("is-enabled");
        clear();
      } else {
        state = true;
        cursor.classList.add("is-enabled");
        update();
      }

      window.addEventListener("resize", () => {
        let width = 0 < window.innerWidth ? window.innerWidth : screen.width;

        if (width < App.config.cursorFollower.disableBreakpoint) {
          state = false;
          cursor.classList.remove("is-enabled");
          clear();
        } else {
          state = true;
          cursor.classList.add("is-enabled");
          update();
        }
      });
    }

    return {
      init: init,
      update: update,
      clear: clear,
      hide: hide,
      show: show,
    };
  })();
  if (document.body.classList.contains("is-parallax-footer")) {
    var body = document.querySelector(".is-parallax-footer");
    initializeParallaxFooter(
      document.getElementById("main"),
      document.getElementById("colophon")
    );
    function initializeParallaxFooter(mainElement, footerElement) {
      footerElement.style.left = "0";
      footerElement.style.right = "0";
      footerElement.style.zIndex = "-1";
      updateParallaxFooter(mainElement, footerElement);
      window.addEventListener("resize", function () {
        updateParallaxFooter(mainElement, footerElement);
      });
      window.addEventListener("scroll", function () {
        updateParallaxFooter(mainElement, footerElement);
      });
    }
    function updateParallaxFooter(mainElement, footerElement) {
      if (isViewportSmallerThanFooter(footerElement)) {
        footerElement.style.bottom = "";
        footerElement.style.top = "0";
      } else {
        footerElement.style.top = "";
        footerElement.style.bottom = "0";
      }
      if (window.scrollY > getBottomY(mainElement)) {
        footerElement.style.position = "static";
        body.style.marginBottom = "0";
      } else {
        body.style.marginBottom = footerElement.offsetHeight + "px";
        footerElement.style.position = "fixed";
      }
    }
    function isViewportSmallerThanFooter(footerElement) {
      return window.innerHeight < footerElement.offsetHeight;
    }
    function getBottomY(element) {
      return element.offsetTop + element.offsetHeight;
    }
  }
  /****** // Start --> Dark / Light Mode Setup // ******/
	document.addEventListener('DOMContentLoaded', function() {
		const toggleButton = document.querySelector('.bloglo-darkmode input[type="checkbox"]');
    if (toggleButton) {
      // Initialize dark mode based on customizer setting or local storage
      let darkModeEnabled = bloglo_vars.dark_mode || localStorage.getItem('darkmode') === 'dark';
      updateDarkMode(darkModeEnabled);
    
      // Toggle button click handler
      toggleButton.addEventListener('change', function() {
        darkModeEnabled = !darkModeEnabled; // Toggle dark mode state
        updateDarkMode(darkModeEnabled); // Update UI and store preference
      });
    
      function updateDarkMode(enabled) {
        if (enabled) {
          document.documentElement.setAttribute('data-theme', 'dark');
          localStorage.setItem('darkmode', 'dark');
          toggleButton.classList.add('active');
        } else {
          document.documentElement.setAttribute('data-theme', 'light');
          localStorage.setItem('darkmode', 'light');
          toggleButton.classList.remove('active');
        }
      }
    }
	});
})();

//jQuery Script
(function ($) {
  $(document).ready(function () {
    // Time
    if ($("#bloglo-time").length) {
      let ct = setInterval(function () {
        myCurrentTimer();
      }, 1000);
    }
    function myCurrentTimer() {
      let currentDateTime = new Date();
      document.getElementById("bloglo-time").innerHTML =
        currentDateTime.toLocaleTimeString();
    }

    if ($("#bloglo-date").length) {
      const currentDate = new Date();
      const options = {
        weekday: 'short', // Short day name (e.g., "Wed")
        month: 'short',   // Short month name (e.g., "Nov")
        day: 'numeric',   // Day of the month (e.g., "8")
        year: 'numeric',  // Full year (e.g., "2023")
      };
      document.getElementById("bloglo-date").innerHTML = currentDate.toLocaleString(document.documentElement.lang, options);
    }
  

    // ticker news slider events
    let htc = $(".ticker-slider-items");
    let hdir = "left";
    if ($("body").hasClass("rtl")) {
      hrtl = true;
      hdir = "right";
    }
    if (htc.length) {
      let htcM = htc.find(".ticker-slider-wrap").marquee({
        speed: 50,
        duration: 14000,
        gap: 0,
        delayBeforeStart: 0,
        direction: hdir,
        duplicated: true,
        startVisible: true,
        pauseOnHover: true,
      });
      htc.on("click", ".ticker-slider-pause", function () {
        $(this).find("i").toggleClass("fa-pause fa-play");
        htcM.marquee("toggle");
      });
    }

    // Masonry Options
    if ($(".masonries").length) {
      setTimeout(() => {
        $(".masonries").masonry({
          // options
          itemSelector: ".masonries > *",
        });
      }, 500);
    }
  });
})(jQuery);
