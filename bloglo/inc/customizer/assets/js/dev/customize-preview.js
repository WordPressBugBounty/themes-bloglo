/**
 * Update Customizer settings live.
 *
 * @since 1.0.0
 */
(function ($) {
  "use strict";

  // Declare variables
  var api = wp.customize,
    $body = $("body"),
    $head = $("head"),
    $style_tag,
    $link_tag,
    bloglo_visibility_classes =
      "bloglo-hide-mobile bloglo-hide-tablet bloglo-hide-mobile-tablet",
    bloglo_style_tag_collection = [],
    bloglo_link_tag_collection = [];

  /**
   * Helper function to get style tag with id.
   */
  function bloglo_get_style_tag(id) {
    if (bloglo_style_tag_collection[id]) {
      return bloglo_style_tag_collection[id];
    }

    $style_tag = $("head").find("#bloglo-dynamic-" + id);

    if (!$style_tag.length) {
      $("head").append(
        '<style id="bloglo-dynamic-' +
          id +
          '" type="text/css" href="#"></style>'
      );
      $style_tag = $("head").find("#bloglo-dynamic-" + id);
    }

    bloglo_style_tag_collection[id] = $style_tag;

    return $style_tag;
  }

  /**
   * Helper function to get link tag with id.
   */
  function bloglo_get_link_tag(id, url) {
    if (bloglo_link_tag_collection[id]) {
      return bloglo_link_tag_collection[id];
    }

    $link_tag = $("head").find("#bloglo-dynamic-link-" + id);

    if (!$link_tag.length) {
      $("head").append(
        '<link id="bloglo-dynamic-' +
          id +
          '" type="text/css" rel="stylesheet" href="' +
          url +
          '"/>'
      );
      $link_tag = $("head").find("#bloglo-dynamic-link-" + id);
    } else {
      $link_tag.attr("href", url);
    }

    bloglo_link_tag_collection[id] = $link_tag;

    return $link_tag;
  }

  /*
   * Helper function to print visibility classes.
   */
  function bloglo_print_visibility_classes($element, newval) {
    if (!$element.length) {
      return;
    }

    $element.removeClass(bloglo_visibility_classes);

    if ("all" !== newval) {
      $element.addClass("bloglo-" + newval);
    }
  }

  /*
   * Helper function to convert hex to rgba.
   */
  function bloglo_hex2rgba(hex, opacity) {
    if ("rgba" === hex.substring(0, 4)) {
      return hex;
    }

    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF").
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;

    hex = hex.replace(shorthandRegex, function (m, r, g, b) {
      return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

    if (opacity) {
      if (1 < opacity) {
        opacity = 1;
      }

      opacity = "," + opacity;
    }

    if (result) {
      return (
        "rgba(" +
        parseInt(result[1], 16) +
        "," +
        parseInt(result[2], 16) +
        "," +
        parseInt(result[3], 16) +
        opacity +
        ")"
      );
    }

    return false;
  }

  /**
   * Helper function to lighten or darken the provided hex color.
   */
  function bloglo_luminance(hex, percent) {
    // Convert RGB color to HEX.
    if (hex.includes("rgb")) {
      hex = bloglo_rgba2hex(hex);
    }

    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF").
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;

    hex = hex.replace(shorthandRegex, function (m, r, g, b) {
      return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

    var isColor = /^#[0-9A-F]{6}$/i.test(hex);

    if (!isColor) {
      return hex;
    }

    var from, to;

    for (var i = 1; 3 >= i; i++) {
      result[i] = parseInt(result[i], 16);
      from = 0 > percent ? 0 : result[i];
      to = 0 > percent ? result[i] : 255;
      result[i] = result[i] + Math.ceil((to - from) * percent);
    }

    result =
      "#" +
      bloglo_dec2hex(result[1]) +
      bloglo_dec2hex(result[2]) +
      bloglo_dec2hex(result[3]);

    return result;
  }

  /**
   * Convert dec to hex.
   */
  function bloglo_dec2hex(c) {
    var hex = c.toString(16);
    return 1 == hex.length ? "0" + hex : hex;
  }

  /**
   * Convert rgb to hex.
   */
  function bloglo_rgba2hex(c) {
    var a, x;

    a = c.split("(")[1].split(")")[0].trim();
    a = a.split(",");

    var result = "";

    for (var i = 0; 3 > i; i++) {
      x = parseInt(a[i]).toString(16);
      result += 1 === x.length ? "0" + x : x;
    }

    if (result) {
      return "#" + result;
    }

    return false;
  }

  /**
   * Check if is light color.
   */
  function bloglo_is_light_color(color = "") {
    var r, g, b, brightness;

    if (color.match(/^rgb/)) {
      color = color.match(
        /^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/
      );
      r = color[1];
      g = color[2];
      b = color[3];
    } else {
      color = +(
        "0x" + color.slice(1).replace(5 > color.length && /./g, "$&$&")
      );
      r = color >> 16;
      g = (color >> 8) & 255;
      b = color & 255;
    }

    brightness = (r * 299 + g * 587 + b * 114) / 1000;

    return 137 < brightness;
  }

  /**
   * Detect if we should use a light or dark color on a background color.
   */
  function bloglo_light_or_dark(color, dark = "#000000", light = "#FFFFFF") {
    return bloglo_is_light_color(color) ? dark : light;
  }

  /**
   * Spacing field CSS.
   */
  function bloglo_spacing_field_css(selector, property, setting, responsive) {
    if (!Array.isArray(setting) && "object" !== typeof setting) {
      return;
    }

    // Set up unit.
    var unit = "px",
      css = "";

    if ("unit" in setting) {
      unit = setting.unit;
    }

    var before = "",
      after = "";

    Object.keys(setting).forEach(function (index, el) {
      if ("unit" === index) {
        return;
      }

      if (responsive) {
        if ("tablet" === index) {
          before = "@media only screen and (max-width: 768px) {";
          after = "}";
        } else if ("mobile" === index) {
          before = "@media only screen and (max-width: 480px) {";
          after = "}";
        } else {
          before = "";
          after = "";
        }

        css += before + selector + "{";

        Object.keys(setting[index]).forEach(function (position) {
          if ("border" === property) {
            position += "-width";
          }

          if (setting[index][position]) {
            css +=
              property +
              "-" +
              position +
              ": " +
              setting[index][position] +
              unit +
              ";";
          }
        });

        css += "}" + after;
      } else {
        if ("border" === property) {
          index += "-width";
        }

        css += property + "-" + index + ": " + setting[index] + unit + ";";
      }
    });

    if (!responsive) {
      css = selector + "{" + css + "}";
    }

    return css;
  }

  /**
   * Range field CSS.
   */
  function bloglo_range_field_css(
    selector,
    property,
    setting,
    responsive,
    unit
  ) {
    var css = "",
      before = "",
      after = "";

    if (responsive && (Array.isArray(setting) || "object" === typeof setting)) {
      Object.keys(setting).forEach(function (index, el) {
        if (setting[index]) {
          if ("tablet" === index) {
            before = "@media only screen and (max-width: 768px) {";
            after = "}";
          } else if ("mobile" === index) {
            before = "@media only screen and (max-width: 480px) {";
            after = "}";
          } else if ("desktop" === index) {
            before = "";
            after = "";
          } else {
            return;
          }

          css +=
            before +
            selector +
            "{" +
            property +
            ": " +
            setting[index] +
            unit +
            "; }" +
            after;
        }
      });
    }

    if (!responsive) {
      if (setting.value) {
        setting = setting.value;
      } else {
        setting = 0;
      }

      css = selector + "{" + property + ": " + setting + unit + "; }";
    }

    return css;
  }

  /**
   * Typography field CSS.
   */
  function bloglo_typography_field_css(selector, setting) {
    var css = "";

    css += selector + "{";

    if ("default" === setting["font-family"]) {
      css +=
        "font-family: " + bloglo_customizer_preview.default_system_font + ";";
    } else if (
      setting["font-family"] in
      bloglo_customizer_preview.fonts.standard_fonts.fonts
    ) {
      css +=
        "font-family: " +
        bloglo_customizer_preview.fonts.standard_fonts.fonts[
          setting["font-family"]
        ].fallback +
        ";";
    } else if ("inherit" !== setting["font-family"]) {
      css += 'font-family: "' + setting["font-family"] + '";';
    }

    css += "font-weight:" + setting["font-weight"] + ";";
    css += "font-style:" + setting["font-style"] + ";";
    css += "text-transform:" + setting["text-transform"] + ";";

    if ("text-decoration" in setting) {
      css += "text-decoration:" + setting["text-decoration"] + ";";
    }

    if ("letter-spacing" in setting) {
      css +=
        "letter-spacing:" +
        setting["letter-spacing"] +
        setting["letter-spacing-unit"] +
        ";";
    }

    if ("line-height-desktop" in setting) {
      css += "line-height:" + setting["line-height-desktop"] + ";";
    }

    if ("font-size-desktop" in setting && "font-size-unit" in setting) {
      css +=
        "font-size:" +
        setting["font-size-desktop"] +
        setting["font-size-unit"] +
        ";";
    }

    css += "}";

    if ("font-size-tablet" in setting && setting["font-size-tablet"]) {
      css +=
        "@media only screen and (max-width: 768px) {" +
        selector +
        "{" +
        "font-size: " +
        setting["font-size-tablet"] +
        setting["font-size-unit"] +
        ";" +
        "}" +
        "}";
    }

    if ("line-height-tablet" in setting && setting["line-height-tablet"]) {
      css +=
        "@media only screen and (max-width: 768px) {" +
        selector +
        "{" +
        "line-height:" +
        setting["line-height-tablet"] +
        ";" +
        "}" +
        "}";
    }

    if ("font-size-mobile" in setting && setting["font-size-mobile"]) {
      css +=
        "@media only screen and (max-width: 480px) {" +
        selector +
        "{" +
        "font-size: " +
        setting["font-size-mobile"] +
        setting["font-size-unit"] +
        ";" +
        "}" +
        "}";
    }

    if ("line-height-mobile" in setting && setting["line-height-mobile"]) {
      css +=
        "@media only screen and (max-width: 480px) {" +
        selector +
        "{" +
        "line-height:" +
        setting["line-height-mobile"] +
        ";" +
        "}" +
        "}";
    }

    return css;
  }

  /**
   * Load google font.
   */
  function bloglo_enqueue_google_font(font) {
    if (bloglo_customizer_preview.fonts.google_fonts.fonts[font]) {
      var id = "google-font-" + font.trim().toLowerCase().replace(" ", "-");
      var url =
        bloglo_customizer_preview.google_fonts_url +
        "/css?family=" +
        font +
        ":" +
        bloglo_customizer_preview.google_font_weights;

      var tag = bloglo_get_link_tag(id, url);
    }
  }

  /**
   * Design Options field CSS.
   */
  function bloglo_design_options_css(selector, setting, type) {
    var css = "",
      before = "",
      after = "";

    if ("background" === type) {
      var bg_type = setting["background-type"];

      css += selector + "{";

      if ("color" === bg_type) {
        setting["background-color"] = setting["background-color"]
          ? setting["background-color"]
          : "inherit";
        css += "background: " + setting["background-color"] + ";";
      } else if ("gradient" === bg_type) {
        css += "background: " + setting["gradient-color-1"] + ";";

        if ("linear" === setting["gradient-type"]) {
          css +=
            "background: -webkit-linear-gradient(" +
            setting["gradient-linear-angle"] +
            "deg, " +
            setting["gradient-color-1"] +
            " " +
            setting["gradient-color-1-location"] +
            "%, " +
            setting["gradient-color-2"] +
            " " +
            setting["gradient-color-2-location"] +
            "%);" +
            "background: -o-linear-gradient(" +
            setting["gradient-linear-angle"] +
            "deg, " +
            setting["gradient-color-1"] +
            " " +
            setting["gradient-color-1-location"] +
            "%, " +
            setting["gradient-color-2"] +
            " " +
            setting["gradient-color-2-location"] +
            "%);" +
            "background: linear-gradient(" +
            setting["gradient-linear-angle"] +
            "deg, " +
            setting["gradient-color-1"] +
            " " +
            setting["gradient-color-1-location"] +
            "%, " +
            setting["gradient-color-2"] +
            " " +
            setting["gradient-color-2-location"] +
            "%);";
        } else if ("radial" === setting["gradient-type"]) {
          css +=
            "background: -webkit-radial-gradient(" +
            setting["gradient-position"] +
            ", circle, " +
            setting["gradient-color-1"] +
            " " +
            setting["gradient-color-1-location"] +
            "%, " +
            setting["gradient-color-2"] +
            " " +
            setting["gradient-color-2-location"] +
            "%);" +
            "background: -o-radial-gradient(" +
            setting["gradient-position"] +
            ", circle, " +
            setting["gradient-color-1"] +
            " " +
            setting["gradient-color-1-location"] +
            "%, " +
            setting["gradient-color-2"] +
            " " +
            setting["gradient-color-2-location"] +
            "%);" +
            "background: radial-gradient(circle at " +
            setting["gradient-position"] +
            ", " +
            setting["gradient-color-1"] +
            " " +
            setting["gradient-color-1-location"] +
            "%, " +
            setting["gradient-color-2"] +
            " " +
            setting["gradient-color-2-location"] +
            "%);";
        }
      } else if ("image" === bg_type) {
        css +=
          "" +
          "background-image: url(" +
          setting["background-image"] +
          ");" +
          "background-size: " +
          setting["background-size"] +
          ";" +
          "background-attachment: " +
          setting["background-attachment"] +
          ";" +
          "background-position: " +
          setting["background-position-x"] +
          "% " +
          setting["background-position-y"] +
          "%;" +
          "background-repeat: " +
          setting["background-repeat"] +
          ";";
      }

      css += "}";

      // Background image color overlay.
      if (
        "image" === bg_type &&
        setting["background-color-overlay"] &&
        setting["background-image"]
      ) {
        css +=
          selector +
          "::after { background-color: " +
          setting["background-color-overlay"] +
          "; }";
      } else {
        css += selector + "::after { background-color: initial; }";
      }
    } else if ("color" === type) {
      setting["text-color"] = setting["text-color"]
        ? setting["text-color"]
        : "inherit";
      setting["link-color"] = setting["link-color"]
        ? setting["link-color"]
        : "inherit";
      setting["link-hover-color"] = setting["link-hover-color"]
        ? setting["link-hover-color"]
        : "inherit";

      css += selector + " { color: " + setting["text-color"] + "; }";
      css += selector + " a { color: " + setting["link-color"] + "; }";
      css +=
        selector +
        " a:hover { color: " +
        setting["link-hover-color"] +
        " !important; }";
    } else if ("border" === type) {
      setting["border-color"] = setting["border-color"]
        ? setting["border-color"]
        : "inherit";
      setting["border-style"] = setting["border-style"]
        ? setting["border-style"]
        : "solid";
      setting["border-left-width"] = setting["border-left-width"]
        ? setting["border-left-width"]
        : 0;
      setting["border-top-width"] = setting["border-top-width"]
        ? setting["border-top-width"]
        : 0;
      setting["border-right-width"] = setting["border-right-width"]
        ? setting["border-right-width"]
        : 0;
      setting["border-bottom-width"] = setting["border-bottom-width"]
        ? setting["border-bottom-width"]
        : 0;

      css += selector + "{";
      css += "border-color: " + setting["border-color"] + ";";
      css += "border-style: " + setting["border-style"] + ";";
      css += "border-left-width: " + setting["border-left-width"] + "px;";
      css += "border-top-width: " + setting["border-top-width"] + "px;";
      css += "border-right-width: " + setting["border-right-width"] + "px;";
      css += "border-bottom-width: " + setting["border-bottom-width"] + "px;";
      css += "}";
    } else if ("separator_color" === type) {
      css +=
        selector +
        ":after{ background-color: " +
        setting["separator-color"] +
        "; }";
    }

    return css;
  }

  /**
   * Logo max height.
   */
  api("bloglo_logo_max_height", function (value) {
    value.bind(function (newval) {
      var $logo = $(".bloglo-logo");

      if (!$logo.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_logo_max_height");
      var style_css = "";

      style_css += bloglo_range_field_css(
        ".bloglo-logo img",
        "max-height",
        newval,
        true,
        "px"
      );
      style_css += bloglo_range_field_css(
        ".bloglo-logo img.bloglo-svg-logo",
        "height",
        newval,
        true,
        "px"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Logo text font size.
   */
  api("bloglo_logo_text_font_size", function (value) {
    value.bind(function (newval) {
      var $logo = $("#bloglo-header .bloglo-logo .site-title");

      if (!$logo.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_logo_text_font_size");
      var style_css = "";

      style_css += bloglo_range_field_css(
        "#bloglo-header .bloglo-logo .site-title",
        "font-size",
        newval,
        true,
        newval.unit
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Logo margin.
   */
  api("bloglo_logo_margin", function (value) {
    value.bind(function (newval) {
      var $logo = $(".bloglo-logo");

      if (!$logo.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_logo_margin");

      var style_css = bloglo_spacing_field_css(
        ".bloglo-logo .logo-inner",
        "margin",
        newval,
        true
      );
      $style_tag.html(style_css);
    });
  });

  /**
   * Tagline.
   */
  api("blogdescription", function (value) {
    value.bind(function (newval) {
      if ($(".bloglo-logo").find(".site-description").length) {
        $(".bloglo-logo").find(".site-description").html(newval);
      }
    });
  });

  /**
   * Site Title.
   */
  api("blogname", function (value) {
    value.bind(function (newval) {
      if ($(".bloglo-logo").find(".site-title").length) {
        $(".bloglo-logo").find(".site-title").find("a").html(newval);
      }
    });
  });

  /**
   * Site Layout.
   */
  api("bloglo_site_layout", function (value) {
    value.bind(function (newval) {
      $body.removeClass(function (index, className) {
        return (
          className.match(/(^|\s)bloglo-layout__(?!boxed-separated)\S+/g) || []
        ).join(" ");
      });

      $body.addClass("bloglo-layout__" + newval);
    });
  });

  /**
   * Sticky Sidebar.
   */
  api("bloglo_sidebar_sticky", function (value) {
    value.bind(function (newval) {
      $body.removeClass(function (index, className) {
        return (className.match(/(^|\s)bloglo-sticky-\S+/g) || []).join(" ");
      });

      if (newval) {
        $body.addClass("bloglo-sticky-" + newval);
      }
    });
  });

  /**
   * Sidebar width.
   */
  api("bloglo_sidebar_width", function (value) {
    value.bind(function (newval) {
      var $sidebar = $("#secondary");

      if (!$sidebar.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_sidebar_width");
      var style_css = "#secondary { width: " + newval.value + "%; }";
      style_css +=
        "body:not(.bloglo-no-sidebar) #primary { " +
        "max-width: " +
        (100 - parseInt(newval.value)) +
        "%;" +
        "};";

      $style_tag.html(style_css);
    });
  });

  /**
   * Sidebar style.
   */
  api("bloglo_sidebar_style", function (value) {
    value.bind(function (newval) {
      $body.removeClass(function (index, className) {
        return (className.match(/(^|\s)bloglo-sidebar-style-\S+/g) || []).join(
          " "
        );
      });

      $body.addClass("bloglo-sidebar-style-" + newval);
    });
  });

  /**
   * Responsive sidebar position.
   */
  api("bloglo_sidebar_responsive_position", function (value) {
    value.bind(function (newval) {
      $body.removeClass(function (index, className) {
        return (className.match(/(^|\s)bloglo-sidebar-r__\S+/g) || []).join(
          " "
        );
      });

      if (newval) {
        $body.addClass("bloglo-sidebar-r__" + newval);
      }
    });
  });

  /**
   * Featured Image Position (Horizontal Blog layout)
   */
  api("bloglo_blog_image_position", function (value) {
    value.bind(function (newval) {
      $(".bloglo-blog-entry-wrapper").removeClass(function (index, className) {
        return (className.match(/(^|\s)bloglo-thumb-\S+/g) || []).join(" ");
      });

      $(".bloglo-blog-entry-wrapper").addClass("bloglo-thumb-" + newval);
    });
  });

  /**
   * Single page - title in header alignment.
   */
  api("bloglo_single_title_alignment", function (value) {
    value.bind(function (newval) {
      $body.removeClass(function (index, className) {
        return (
          className.match(/(^|\s)bloglo-page-title-align-\S+/g) || []
        ).join(" ");
      });

      $body.addClass("bloglo-page-title-align-" + newval);
    });
  });

  /**
   * Single Page title spacing.
   */
  api("bloglo_single_title_spacing", function (value) {
    value.bind(function (newval) {
      var $page_header = $(".page-header");

      if (!$page_header.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_single_title_spacing");

      var style_css = bloglo_spacing_field_css(
        ".bloglo-single-title-in-page-header #page .page-header .bloglo-page-header-wrapper",
        "padding",
        newval,
        true
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Single post narrow container width.
   */
  api("bloglo_single_narrow_container_width", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_single_narrow_container_width");
      var style_css = "";

      style_css +=
        '.single-post.narrow-content .entry-content > :not([class*="align"]):not([class*="gallery"]):not(.wp-block-image):not(.quote-inner):not(.quote-post-bg), ' +
        '.single-post.narrow-content .mce-content-body:not([class*="page-template-full-width"]) > :not([class*="align"]):not([data-wpview-type*="gallery"]):not(blockquote):not(.mceTemp), ' +
        ".single-post.narrow-content .entry-footer, " +
        ".single-post.narrow-content .post-nav, " +
        ".single-post.narrow-content .entry-content > .alignwide, " +
        ".single-post.narrow-content p.has-background:not(.alignfull):not(.alignwide)" +
        ".single-post.narrow-content #bloglo-comments-toggle, " +
        ".single-post.narrow-content #comments, " +
        ".single-post.narrow-content .entry-content .aligncenter, " +
        ".single-post.narrow-content .bloglo-narrow-element, " +
        ".single-post.narrow-content.bloglo-single-title-in-content .entry-header, " +
        ".single-post.narrow-content.bloglo-single-title-in-content .entry-meta, " +
        ".single-post.narrow-content.bloglo-single-title-in-content .post-category, " +
        ".single-post.narrow-content.bloglo-no-sidebar .bloglo-page-header-wrapper, " +
        ".single-post.narrow-content.bloglo-no-sidebar .bloglo-breadcrumbs > .bloglo-container > nav {" +
        "max-width: " +
        parseInt(newval.value) +
        "px; margin-left: auto; margin-right: auto; " +
        "}";

      style_css +=
        ".single-post.narrow-content .author-box, " +
        ".single-post.narrow-content .entry-content > .alignwide { " +
        "max-width: " +
        (parseInt(newval.value) + 70) +
        "px;" +
        "}";

      $style_tag.html(style_css);
    });
  });

  /**
   * Header container width.
   */
  api("bloglo_header_container_width", function (value) {
    value.bind(function (newval) {
      var $header = $("#bloglo-header");

      if (!$header.length) {
        return;
      }

      if ("full-width" === newval) {
        $header.addClass("bloglo-container__wide");
      } else {
        $header.removeClass("bloglo-container__wide");
      }
    });
  });

  /**
   * Main navigation disply breakpoint.
   */
  api("bloglo_main_nav_mobile_breakpoint", function (value) {
    value.bind(function (newval) {
      var $nav = $("#bloglo-header-inner .bloglo-nav");

      if (!$nav.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_main_nav_mobile_breakpoint");
      var style_css = "";

      style_css +=
        "@media screen and (min-width: " +
        parseInt(newval) +
        "px) {#bloglo-header-inner .bloglo-nav {display:flex} .bloglo-mobile-nav,.bloglo-mobile-toggen,#bloglo-header-inner .bloglo-nav .menu-item-has-children>a > .bloglo-icon,#bloglo-header-inner .bloglo-nav .page_item_has_children>a > .bloglo-icon {display:none;} }";
      style_css +=
        "@media screen and (max-width: " +
        parseInt(newval) +
        "px) {#bloglo-header-inner .bloglo-nav {display:none} .bloglo-mobile-nav,.bloglo-mobile-toggen {display:inline-flex;} }";

      $style_tag.html(style_css);
    });
  });

  /**
   * Mobile Menu Button Label.
   */
  api("bloglo_main_nav_mobile_label", function (value) {
    value.bind(function (newval) {
      if (
        $(".bloglo-hamburger-bloglo-primary-nav").find(".hamburger-label")
          .length
      ) {
        $(".bloglo-hamburger-bloglo-primary-nav")
          .find(".hamburger-label")
          .html(newval);
      }
    });
  });

  /**
   * Main Nav Font color.
   */
  api("bloglo_main_nav_font_color", function (value) {
    value.bind(function (newval) {
      var $navigation = $("#bloglo-header-inner .bloglo-nav");

      if (!$navigation.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_main_nav_font_color");
      var style_css = "";

      // Link color.
      newval["link-color"] = newval["link-color"]
        ? newval["link-color"]
        : "inherit";
      style_css +=
        "#bloglo-header-inner .bloglo-nav > ul > li > a { color: " +
        newval["link-color"] +
        "; }";

      // Link hover color.
      newval["link-hover-color"] = newval["link-hover-color"]
        ? newval["link-hover-color"]
        : api.value("bloglo_accent_color")();
      style_css +=
        "#bloglo-header-inner .bloglo-nav > ul > li > a:hover, " +
        "#bloglo-header-inner .bloglo-nav > ul > li.menu-item-has-children:hover > a, " +
        "#bloglo-header-inner .bloglo-nav > ul > li.current-menu-item > a, " +
        "#bloglo-header-inner .bloglo-nav > ul > li.current-menu-ancestor > a " +
        "#bloglo-header-inner .bloglo-nav > ul > li.page_item_has_children:hover > a, " +
        "#bloglo-header-inner .bloglo-nav > ul > li.current_page_item > a, " +
        "#bloglo-header-inner .bloglo-nav > ul > li.current_page_ancestor > a " +
        "{ color: " +
        newval["link-hover-color"] +
        "; }";

      $style_tag.html(style_css);
    });
  });

  /**
   * Main Nav Background.
   */
  api("bloglo_main_nav_background", function (value) {
    value.bind(function (newval) {
      var $navigation = $(".bloglo-header-layout-3 .bloglo-nav-container");

      if (!$navigation.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_main_nav_background");
      var style_css = bloglo_design_options_css(
        ".bloglo-header-layout-3 .bloglo-nav-container",
        newval,
        "background"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Main Nav Border.
   */
  api("bloglo_main_nav_border", function (value) {
    value.bind(function (newval) {
      var $navigation = $(".bloglo-header-layout-3 .bloglo-nav-container");

      if (!$navigation.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_main_nav_border");
      var style_css = bloglo_design_options_css(
        ".bloglo-header-layout-3 .bloglo-nav-container",
        newval,
        "border"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Main Nav font size.
   */
  api("bloglo_main_nav_font_size", function (value) {
    value.bind(function (newval) {
      var $nav = $("#bloglo-header-inner");

      if (!$nav.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_main_nav_font_size");
      var style_css = "";

      style_css += bloglo_range_field_css(
        ".bloglo-nav.bloglo-header-element, .bloglo-header-layout-1 .bloglo-header-widgets, .bloglo-header-layout-2 .bloglo-header-widgets",
        "font-size",
        newval,
        false,
        newval.unit
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Top Bar container width.
   */
  api("bloglo_top_bar_container_width", function (value) {
    value.bind(function (newval) {
      var $topbar = $("#bloglo-topbar");

      if (!$topbar.length) {
        return;
      }

      if ("full-width" === newval) {
        $topbar.addClass("bloglo-container__wide");
      } else {
        $topbar.removeClass("bloglo-container__wide");
      }
    });
  });

  /**
   * Top Bar visibility.
   */
  api("bloglo_top_bar_visibility", function (value) {
    value.bind(function (newval) {
      var $topbar = $("#bloglo-topbar");

      bloglo_print_visibility_classes($topbar, newval);
    });
  });

  /**
   * Top Bar widgets separator.
   */
  api("bloglo_top_bar_widgets_separator", function (value) {
    value.bind(function (newval) {
      $body.removeClass(function (index, className) {
        return (
          className.match(/(^|\s)bloglo-topbar__separators-\S+/g) || []
        ).join(" ");
      });

      $body.addClass("bloglo-topbar__separators-" + newval);
    });
  });

  /**
   * Top Bar background.
   */
  api("bloglo_top_bar_background", function (value) {
    value.bind(function (newval) {
      var $topbar = $("#bloglo-topbar");

      if (!$topbar.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_top_bar_background");
      var style_css = bloglo_design_options_css(
        "#bloglo-topbar",
        newval,
        "background"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Top Bar color.
   */
  api("bloglo_top_bar_text_color", function (value) {
    value.bind(function (newval) {
      var $topbar = $("#bloglo-topbar");

      if (!$topbar.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_top_bar_text_color");
      var style_css = "";

      newval["text-color"] = newval["text-color"]
        ? newval["text-color"]
        : "inherit";
      newval["link-color"] = newval["link-color"]
        ? newval["link-color"]
        : "inherit";
      newval["link-hover-color"] = newval["link-hover-color"]
        ? newval["link-hover-color"]
        : "inherit";

      // Text color.
      style_css += "#bloglo-topbar { color: " + newval["text-color"] + "; }";

      // Link color.
      style_css +=
        ".bloglo-topbar-widget__text a, " +
        ".bloglo-topbar-widget .bloglo-nav > ul > li > a, " +
        ".bloglo-topbar-widget__socials .bloglo-social-nav > ul > li > a, " +
        "#bloglo-topbar .bloglo-topbar-widget__text .bloglo-icon { color: " +
        newval["link-color"] +
        "; }";

      // Link hover color.
      style_css +=
        "#bloglo-topbar .bloglo-nav > ul > li > a:hover, " +
        ".using-keyboard #bloglo-topbar .bloglo-nav > ul > li > a:focus," +
        "#bloglo-topbar .bloglo-nav > ul > li.menu-item-has-children:hover > a,  " +
        "#bloglo-topbar .bloglo-nav > ul > li.current-menu-item > a, " +
        "#bloglo-topbar .bloglo-nav > ul > li.current-menu-ancestor > a, " +
        "#bloglo-topbar .bloglo-topbar-widget__text a:hover, " +
        "#bloglo-topbar .bloglo-social-nav > ul > li > a .bloglo-icon.bottom-icon { color: " +
        newval["link-hover-color"] +
        "; }";

      $style_tag.html(style_css);
    });
  });

  /**
   * Top Bar border.
   */
  api("bloglo_top_bar_border", function (value) {
    value.bind(function (newval) {
      var $topbar = $("#bloglo-topbar");

      if (!$topbar.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_top_bar_border");
      var style_css = bloglo_design_options_css(
        "#bloglo-topbar",
        newval,
        "border"
      );

      style_css += bloglo_design_options_css(
        "#bloglo-topbar .bloglo-topbar-widget",
        newval,
        "separator_color"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Header menu item hover animation.
   */
  api("bloglo_main_nav_hover_animation", function (value) {
    value.bind(function (newval) {
      $body.removeClass(function (index, className) {
        return (className.match(/(^|\s)bloglo-menu-animation-\S+/g) || []).join(
          " "
        );
      });

      $body.addClass("bloglo-menu-animation-" + newval);
    });
  });

  /**
   * Header widgets separator.
   */
  api("bloglo_header_widgets_separator", function (value) {
    value.bind(function (newval) {
      $body.removeClass(function (index, className) {
        return (
          className.match(/(^|\s)bloglo-header__separators-\S+/g) || []
        ).join(" ");
      });

      $body.addClass("bloglo-header__separators-" + newval);
    });
  });

  /**
   * Header background.
   */
  api("bloglo_header_background", function (value) {
    value.bind(function (newval) {
      var $header = $("#bloglo-header-inner");

      if (!$header.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_header_background");
      var style_css = bloglo_design_options_css(
        "#bloglo-header-inner",
        newval,
        "background"
      );

      if ("color" === newval["background-type"] && newval["background-color"]) {
        style_css +=
          ".bloglo-header-widget__cart .bloglo-cart .bloglo-cart-count { border: 2px solid " +
          newval["background-color"] +
          "; }";
      } else {
        style_css +=
          ".bloglo-header-widget__cart .bloglo-cart .bloglo-cart-count { border: none; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Header font color.
   */
  api("bloglo_header_text_color", function (value) {
    value.bind(function (newval) {
      var $header = $("#bloglo-header");

      if (!$header.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_header_text_color");
      var style_css = "";

      // Text color.
      style_css +=
        ".bloglo-logo .site-description { color: " +
        newval["text-color"] +
        "; }";

      // Link color.
      if (newval["link-color"]) {
        style_css +=
          "#bloglo-header, " +
          ".bloglo-header-widgets a:not(.bloglo-btn), " +
          ".bloglo-logo a," +
          ".bloglo-hamburger { color: " +
          newval["link-color"] +
          "; }";
        style_css +=
          ".hamburger-inner," +
          ".hamburger-inner::before," +
          ".hamburger-inner::after { background-color: " +
          newval["link-color"] +
          "; }";
      }

      // Link hover color.
      if (newval["link-hover-color"]) {
        style_css +=
          ".bloglo-header-widgets a:not(.bloglo-btn):hover, " +
          "#bloglo-header-inner .bloglo-header-widgets .bloglo-active," +
          ".bloglo-logo .site-title a:hover, " +
          ".bloglo-hamburger:hover .hamburger-label, " +
          ".is-mobile-menu-active .bloglo-hamburger .hamburger-label," +
          "#bloglo-header-inner .bloglo-nav > ul > li > a:hover," +
          "#bloglo-header-inner .bloglo-nav > ul > li.menu-item-has-children:hover > a," +
          "#bloglo-header-inner .bloglo-nav > ul > li.current-menu-item > a," +
          "#bloglo-header-inner .bloglo-nav > ul > li.current-menu-ancestor > a," +
          "#bloglo-header-inner .bloglo-nav > ul > li.page_item_has_children:hover > a," +
          "#bloglo-header-inner .bloglo-nav > ul > li.current_page_item > a," +
          "#bloglo-header-inner .bloglo-nav > ul > li.current_page_ancestor > a { color: " +
          newval["link-hover-color"] +
          "; }";

        style_css +=
          ".bloglo-hamburger:hover .hamburger-inner," +
          ".bloglo-hamburger:hover .hamburger-inner::before," +
          ".bloglo-hamburger:hover .hamburger-inner::after," +
          ".is-mobile-menu-active .bloglo-hamburger .hamburger-inner," +
          ".is-mobile-menu-active .bloglo-hamburger .hamburger-inner::before," +
          ".is-mobile-menu-active .bloglo-hamburger .hamburger-inner::after { background-color: " +
          newval["link-hover-color"] +
          "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Header border.
   */
  api("bloglo_header_border", function (value) {
    value.bind(function (newval) {
      var $header = $("#bloglo-header-inner");

      if (!$header.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_header_border");
      var style_css = bloglo_design_options_css(
        "#bloglo-header-inner",
        newval,
        "border"
      );

      // Separator color.
      newval["separator-color"] = newval["separator-color"]
        ? newval["separator-color"]
        : "inherit";
      style_css +=
        ".bloglo-header-widget:after { background-color: " +
        newval["separator-color"] +
        "; }";

      $style_tag.html(style_css);
    });
  });

  /**
   * Hero container width.
   */
  api("bloglo_hero_hover_slider_container", function (value) {
    value.bind(function (newval) {
      var $hero_container = $("#hero .bloglo-hero-container");

      if (!$hero_container.length) {
        return;
      }

      if ("full-width" === newval) {
        $hero_container.addClass("bloglo-container__wide");
      } else {
        $hero_container.removeClass("bloglo-container__wide");
      }
    });
  });

  /**
   * Hero overlay style.
   */
  api("bloglo_hero_hover_slider_overlay", function (value) {
    value.bind(function (newval) {
      var $hero = $("#hero .bloglo-hover-slider");

      if (!$hero.length) {
        return;
      }

      $hero
        .removeClass(function (index, className) {
          return (className.match(/(^|\s)slider-overlay-\S+/g) || []).join(" ");
        })
        .addClass("slider-overlay-" + newval);
    });
  });

  /**
   * Hero height.
   */
  api("bloglo_hero_hover_slider_height", function (value) {
    value.bind(function (newval) {
      var $hero = $("#hero");

      if (!$hero.length) {
        return;
      }

      $hero.find(".hover-slide-item").css("height", newval.value + "px");
    });
  });

  /**
   * Hero visibility.
   */
  api("bloglo_hero_visibility", function (value) {
    value.bind(function (newval) {
      bloglo_print_visibility_classes($("#hero"), newval);
    });
  });

  /**
   * Featured Links title.
   */
  api("bloglo_featured_links_title", function (value) {
    value.bind(function (newval) {
      $("#featured_links .widget-title").text(newval);
    });
  });

  /**
   * Featured Links container width.
   */
  api("bloglo_featured_links_container", function (value) {
    value.bind(function (newval) {
      var $featured_links_container = $("#featured_links .bloglo-featured-container");

      if (!$featured_links_container.length) {
        return;
      }

      if ("full-width" === newval) {
        $featured_links_container.addClass("bloglo-container__wide");
      } else {
        $featured_links_container.removeClass("bloglo-container__wide");
      }
    });
  });

  /**
   * Featured Links visibility.
   */
  api("bloglo_featured_links_visibility", function (value) {
    value.bind(function (newval) {
      bloglo_print_visibility_classes($("#featured_links"), newval);
    });
  });

  /**
   * PYML title.
   */
  api("bloglo_pyml_title", function (value) {
    value.bind(function (newval) {
      $("#pyml .widget-title").text(newval);
    });
  });

  /**
   * PYML container width.
   */
  api("bloglo_pyml_container", function (value) {
    value.bind(function (newval) {
      var $pyml_container = $("#pyml .bloglo-pyml-container");

      if (!$pyml_container.length) {
        return;
      }

      if ("full-width" === newval) {
        $pyml_container.addClass("bloglo-container__wide");
      } else {
        $pyml_container.removeClass("bloglo-container__wide");
      }
    });
  });

  /**
   * PYML visibility.
   */
  api("bloglo_pyml_visibility", function (value) {
    value.bind(function (newval) {
      bloglo_print_visibility_classes($("#pyml"), newval);
    });
  });

  /**
   * Ticker News title.
   */
  api("bloglo_ticker_title", function (value) {
    value.bind(function (newval) {
      $("#ticker .ticker-title .title").text(newval);
    });
  });

  /**
   * Ticker News container width.
   */
  api("bloglo_ticker_container", function (value) {
    value.bind(function (newval) {
      var $ticker_container = $("#ticker .bloglo-ticker-container");

      if (!$ticker_container.length) {
        return;
      }

      if ("full-width" === newval) {
        $ticker_container.addClass("bloglo-container__wide");
      } else {
        $ticker_container.removeClass("bloglo-container__wide");
      }
    });
  });

  /**
   * Ticker News visibility.
   */
  api("bloglo_ticker_visibility", function (value) {
    value.bind(function (newval) {
      bloglo_print_visibility_classes($("#ticker"), newval);
    });
  });

  /**
   * Custom input style.
   */
  api("bloglo_custom_input_style", function (value) {
    value.bind(function (newval) {
      if (newval) {
        $body.addClass("bloglo-input-supported");
      } else {
        $body.removeClass("bloglo-input-supported");
      }
    });
  });

  /**
   * Pre Footer Call to Action Enable.
   */
  api("bloglo_enable_pre_footer_cta", function (value) {
    value.bind(function (newval) {
      if (newval) {
        $body.addClass(
          "bloglo-pre-footer-cta-style-" +
            api.value("bloglo_pre_footer_cta_style")()
        );
      } else {
        $body.removeClass(function (index, className) {
          return (
            className.match(/(^|\s)bloglo-pre-footer-cta-style-\S+/g) || []
          ).join(" ");
        });
      }
    });
  });

  /**
   * Pre Footer Call to Action visibility.
   */
  api("bloglo_pre_footer_cta_visibility", function (value) {
    value.bind(function (newval) {
      var $cta = $(".bloglo-pre-footer-cta");

      if (!$cta.length) {
        return;
      }

      bloglo_print_visibility_classes($cta, newval);
    });
  });

  /**
   * Pre Footer Call to Action Text.
   */
  api("bloglo_pre_footer_cta_text", function (value) {
    value.bind(function (newval) {
      var $cta = $("#bloglo-pre-footer .bloglo-pre-footer-cta");

      if (!$cta.length) {
        return;
      }

      $cta.find("p.h3").html(newval);
    });
  });

  /**
   * Pre Footer Call to Action Style.
   */
  api("bloglo_pre_footer_cta_style", function (value) {
    value.bind(function (newval) {
      $body
        .removeClass(function (index, className) {
          return (
            className.match(/(^|\s)bloglo-pre-footer-cta-style-\S+/g) || []
          ).join(" ");
        })
        .addClass(
          "bloglo-pre-footer-cta-style-" +
            api.value("bloglo_pre_footer_cta_style")()
        );
    });
  });

  /**
   * Pre Footer Call to Action Button Text.
   */
  api("bloglo_pre_footer_cta_btn_text", function (value) {
    value.bind(function (newval) {
      var $cta = $("#bloglo-pre-footer .bloglo-pre-footer-cta");

      if (!$cta.length) {
        return;
      }

      if (newval) {
        $cta.find("a").css("display", "inline-flex").html(newval);
      } else {
        $cta.find("a").css("display", "none").html("");
      }
    });
  });

  /**
   * Pre Footer Call to Action Background.
   */
  api("bloglo_pre_footer_cta_background", function (value) {
    value.bind(function (newval) {
      var $cta = $("#bloglo-pre-footer .bloglo-pre-footer-cta");

      if (!$cta.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_pre_footer_cta_background");
      var style_css = "";

      if ("color" === newval["background-type"]) {
        style_css += bloglo_design_options_css(
          ".bloglo-pre-footer-cta-style-1 #bloglo-pre-footer .bloglo-flex-row::before, .bloglo-pre-footer-cta-style-2 #bloglo-pre-footer::before",
          newval,
          "background"
        );
        style_css +=
          ".bloglo-pre-footer-cta-style-1 #bloglo-pre-footer .bloglo-flex-row::after," +
          ".bloglo-pre-footer-cta-style-2 #bloglo-pre-footer::after" +
          "{ background-image: none; }";
      } else {
        style_css += bloglo_design_options_css(
          ".bloglo-pre-footer-cta-style-1 #bloglo-pre-footer .bloglo-flex-row::after",
          newval,
          "background"
        );
        style_css += bloglo_design_options_css(
          ".bloglo-pre-footer-cta-style-2 #bloglo-pre-footer::after",
          newval,
          "background"
        );
      }

      if (
        "image" === newval["background-type"] &&
        newval["background-color-overlay"] &&
        newval["background-image"]
      ) {
        style_css +=
          ".bloglo-pre-footer-cta-style-1 #bloglo-pre-footer .bloglo-flex-row::before," +
          ".bloglo-pre-footer-cta-style-2 #bloglo-pre-footer::before" +
          "{ background-color: " +
          newval["background-color-overlay"] +
          "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Pre Footer Call to Action Text Color.
   */
  api("bloglo_pre_footer_cta_text_color", function (value) {
    value.bind(function (newval) {
      var $cta = $("#bloglo-pre-footer .bloglo-pre-footer-cta");

      if (!$cta.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_pre_footer_cta_text_color");
      var style_css = "";

      style_css += bloglo_design_options_css(
        "#bloglo-pre-footer .h2",
        newval,
        "color"
      );
      style_css += bloglo_design_options_css(
        "#bloglo-pre-footer .h3",
        newval,
        "color"
      );
      style_css += bloglo_design_options_css(
        "#bloglo-pre-footer .h4",
        newval,
        "color"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Pre Footer Call to Action Border.
   */
  api("bloglo_pre_footer_cta_border", function (value) {
    value.bind(function (newval) {
      var $cta = $("#bloglo-pre-footer .bloglo-pre-footer-cta");

      if (!$cta.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_pre_footer_cta_border");
      var style_css = bloglo_design_options_css(
        ".bloglo-pre-footer-cta-style-1 #bloglo-pre-footer .bloglo-flex-row::before, .bloglo-pre-footer-cta-style-2 #bloglo-pre-footer::before",
        newval,
        "border"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Pre Footer CTA font size.
   */
  api("bloglo_pre_footer_cta_font_size", function (value) {
    value.bind(function (newval) {
      var $cta = $("#bloglo-pre-footer .bloglo-pre-footer-cta");

      if (!$cta.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_pre_footer_cta_font_size");
      var style_css = bloglo_range_field_css(
        "#bloglo-pre-footer .h3",
        "font-size",
        newval,
        true,
        newval.unit
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * WooCommerce sale badge text.
   */
  api("bloglo_product_sale_badge_text", function (value) {
    value.bind(function (newval) {
      var $badge = $(
        ".woocommerce ul.products li.product .onsale, .woocommerce span.onsale"
      ).not(".sold-out");

      if (!$badge.length) {
        return;
      }

      $badge.html(newval);
    });
  });

  /**
   * Accent color.
   */
  api("bloglo_accent_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_accent_color");
      var style_css;

      // Colors.
      style_css =
        ":root { " +
        "--bloglo-primary: " +
        newval +
        ";" +
        "--bloglo-primary_15: " +
        bloglo_luminance(newval, 0.15) +
        ";" +
        "--bloglo-primary_27: " +
        bloglo_hex2rgba(newval, 0.27) +
        ";" +
        "--bloglo-primary_09: " +
        bloglo_hex2rgba(newval, 0.09) +
        ";" +
        "--bloglo-primary_04: " +
        bloglo_hex2rgba(newval, 0.04) +
        ";" +
        "}";

      // Gradient.
      style_css +=
        ".bloglo-pre-footer-cta-style-1 #bloglo-pre-footer .bloglo-flex-row::before," +
        ".bloglo-pre-footer-cta-style-2 #bloglo-pre-footer::before { " +
        "background: linear-gradient(to right, " +
        bloglo_hex2rgba(newval, 0.9) +
        " 0%, " +
        bloglo_hex2rgba(newval, 0.82) +
        " 35%, " +
        bloglo_hex2rgba(newval, 0.4) +
        " 100% );" +
        "-webkit-gradient(linear, left top, right top, from(" +
        bloglo_hex2rgba(newval, 0.9) +
        "), color-stop(35%, " +
        bloglo_hex2rgba(newval, 0.82) +
        "), to(" +
        bloglo_hex2rgba(newval, 0.4) +
        ")); }";

      $style_tag.html(style_css);
    });
  });

  api( 'bloglo_dark_mode', function( value ) {
		value.bind( function( newval ) {
			if (newval) {
				document.documentElement.setAttribute('data-theme', 'dark');
				localStorage.setItem('darkmode', 'dark');
			} else {
				document.documentElement.setAttribute('data-theme', 'light');
				localStorage.setItem('darkmode', 'light');
			}
		} )
	} );
  /**
   * Content background color.
   */
  api("bloglo_boxed_content_background_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag(
        "bloglo_boxed_content_background_color"
      );
      var style_css = "";

      if (newval) {
        style_css =
          ".bloglo-layout__boxed #page, " +
          ".bloglo-layout__boxed-separated .ticker-slider-items, " +
          ".bloglo-layout__boxed-separated .pyml-slider-items, " +
          ".bloglo-layout__boxed-separated.author .author-box, " +
          ".bloglo-layout__boxed-separated #content, " +
          ".bloglo-layout__boxed-separated.bloglo-sidebar-style-3 #secondary .bloglo-widget, " +
          ".bloglo-layout__boxed-separated.bloglo-sidebar-style-3 .elementor-widget-sidebar .bloglo-widget, " +
          ".bloglo-layout__boxed-separated.archive .bloglo-article, " +
          ".bloglo-layout__boxed-separated.blog .bloglo-article, " +
          ".bloglo-layout__boxed-separated.search-results .bloglo-article, " +
          ".bloglo-layout__boxed-separated.category .bloglo-article { background-color: " +
          newval +
          "; }";

        // style_css += '@media screen and (max-width: 960px) { ' + '.bloglo-layout__boxed-separated #page { background-color: ' + newval + '; }' + '}';
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Content text color.
   */
  api("bloglo_content_text_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_content_text_color");
      var style_css = "";

      if (newval) {
        style_css =
          "body { " +
          "color: " +
          newval +
          ";" +
          "}" +
          ":root { " +
          "--bloglo-secondary_38: " +
          newval +
          ";" +
          "}" +
          ".comment-form .comment-notes, " +
          "#comments .no-comments, " +
          "#page .wp-caption .wp-caption-text," +
          "#comments .comment-meta," +
          ".comments-closed," +
          ".entry-meta," +
          ".bloglo-entry cite," +
          "legend," +
          ".bloglo-page-header-description," +
          ".page-links em," +
          ".site-content .page-links em," +
          ".single .entry-footer .last-updated," +
          ".single .post-nav .post-nav-title," +
          "#main .widget_recent_comments span," +
          "#main .widget_recent_entries span," +
          "#main .widget_calendar table > caption," +
          ".post-thumb-caption, " +
          ".wp-block-image figcaption, " +
          ".bloglo-cart-item .bloglo-x," +
          ".woocommerce form.login .lost_password a," +
          ".woocommerce form.register .lost_password a," +
          ".woocommerce a.remove," +
          "#add_payment_method .cart-collaterals .cart_totals .woocommerce-shipping-destination, " +
          ".woocommerce-cart .cart-collaterals .cart_totals .woocommerce-shipping-destination, " +
          ".woocommerce-checkout .cart-collaterals .cart_totals .woocommerce-shipping-destination," +
          ".woocommerce ul.products li.product .bloglo-loop-product__category-wrap a," +
          ".woocommerce ul.products li.product .bloglo-loop-product__category-wrap," +
          ".woocommerce .woocommerce-checkout-review-order table.shop_table thead th," +
          "#add_payment_method #payment div.payment_box, " +
          ".woocommerce-cart #payment div.payment_box, " +
          ".woocommerce-checkout #payment div.payment_box," +
          "#add_payment_method #payment ul.payment_methods .about_paypal, " +
          ".woocommerce-cart #payment ul.payment_methods .about_paypal, " +
          ".woocommerce-checkout #payment ul.payment_methods .about_paypal," +
          ".woocommerce table dl," +
          ".woocommerce table .wc-item-meta," +
          ".widget.woocommerce .reviewer," +
          ".woocommerce.widget_shopping_cart .cart_list li a.remove:before," +
          ".woocommerce .widget_shopping_cart .cart_list li a.remove:before," +
          ".woocommerce .widget_shopping_cart .cart_list li .quantity, " +
          ".woocommerce.widget_shopping_cart .cart_list li .quantity," +
          ".woocommerce div.product .woocommerce-product-rating .woocommerce-review-link," +
          ".woocommerce div.product .woocommerce-tabs table.shop_attributes td," +
          ".woocommerce div.product .product_meta > span span:not(.bloglo-woo-meta-title), " +
          ".woocommerce div.product .product_meta > span a," +
          ".woocommerce .star-rating::before," +
          ".woocommerce div.product #reviews #comments ol.commentlist li .comment-text p.meta," +
          ".ywar_review_count," +
          ".woocommerce .add_to_cart_inline del, " +
          ".woocommerce div.product p.price del, " +
          ".woocommerce div.product span.price del { color: " +
          bloglo_hex2rgba(newval, 0.75) +
          "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Content link hover color.
   */
  api("bloglo_content_link_hover_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_content_link_hover_color");
      var style_css = "";

      if (newval) {
        // Content link hover.
        style_css +=
          ".content-area a:not(.bloglo-btn, .wp-block-button__link, .page-numbers, [rel^=category]):hover, " +
          ".bloglo-woo-before-shop select.custom-select-loaded:hover ~ #bloglo-orderby, " +
          "#add_payment_method #payment ul.payment_methods .about_paypal:hover, " +
          ".woocommerce-cart #payment ul.payment_methods .about_paypal:hover, " +
          ".woocommerce-checkout #payment ul.payment_methods .about_paypal:hover, " +
          ".bloglo-breadcrumbs a:hover, " +
          ".woocommerce div.product .woocommerce-product-rating .woocommerce-review-link:hover, " +
          ".woocommerce ul.products li.product .meta-wrap .woocommerce-loop-product__link:hover, " +
          ".woocommerce ul.products li.product .bloglo-loop-product__category-wrap a:hover { " +
          "color: " +
          newval +
          ";" +
          "}";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Content text color.
   */
  api("bloglo_headings_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_headings_color");
      var style_css = "";

      if (newval) {
        style_css =
          "h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .bloglo-logo .site-title, .error-404 .page-header h1 { " +
          "color: " +
          newval +
          ";" +
          "} :root { " +
          "--bloglo-secondary: " +
          newval +
          ";" +
          "}";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Scroll Top visibility.
   */
  api("bloglo_scroll_top_visibility", function (value) {
    value.bind(function (newval) {
      bloglo_print_visibility_classes($("#bloglo-scroll-top"), newval);
    });
  });

  /**
   * Page Preloader visibility.
   */
  api("bloglo_preloader_visibility", function (value) {
    value.bind(function (newval) {
      bloglo_print_visibility_classes($("#bloglo-preloader"), newval);
    });
  });

  /**
   * Footer visibility.
   */
  api("bloglo_footer_visibility", function (value) {
    value.bind(function (newval) {
      bloglo_print_visibility_classes($("#bloglo-footer"), newval);
    });
  });

  /**
   * Footer Widget Heading Style Enable.
   */
  api("bloglo_footer_widget_heading_style", function (value) {
    value.bind(function (newval) {
      $body
        .removeClass(function (index, className) {
          return (
            className.match(/(^|\s)is-footer-heading-init-s\S+/g) || []
          ).join(" ");
        })
        .addClass(
          "is-footer-heading-init-s" +
            api.value("bloglo_footer_widget_heading_style")()
        );
    });
  });

  /**
   * Footer background.
   */
  api("bloglo_footer_background", function (value) {
    value.bind(function (newval) {
      var $footer = $("#colophon");

      if (!$footer.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_footer_background");
      var style_css = bloglo_design_options_css(
        "#colophon",
        newval,
        "background"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Footer font color.
   */
  api("bloglo_footer_text_color", function (value) {
    var $footer = $("#bloglo-footer"),
      copyright_separator_color,
      style_css;

    value.bind(function (newval) {
      if (!$footer.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_footer_text_color");

      style_css = "";

      newval["text-color"] = newval["text-color"]
        ? newval["text-color"]
        : "inherit";
      newval["link-color"] = newval["link-color"]
        ? newval["link-color"]
        : "inherit";
      newval["link-hover-color"] = newval["link-hover-color"]
        ? newval["link-hover-color"]
        : "inherit";
      newval["widget-title-color"] = newval["widget-title-color"]
        ? newval["widget-title-color"]
        : "inherit";

      // Text color.
      style_css += "#colophon { color: " + newval["text-color"] + "; }";

      // Link color.
      style_css += "#colophon a { color: " + newval["link-color"] + "; }";

      // Link hover color.
      style_css +=
        "#colophon a:hover, #colophon li.current_page_item > a, #colophon .bloglo-social-nav > ul > li > a .bloglo-icon.bottom-icon " +
        "{ color: " +
        newval["link-hover-color"] +
        "; }";

      // Widget title color.
      style_css +=
        "#colophon .widget-title, #colophon .wp-block-heading { color: " +
        newval["widget-title-color"] +
        "; }";

      // Copyright separator color.
      copyright_separator_color = bloglo_light_or_dark(
        newval["text-color"],
        "rgba(255,255,255,0.1)",
        "rgba(0,0,0,0.1)"
      );

      // copyright_separator_color = bloglo_luminance( newval['text-color'], 0.8 );

      style_css +=
        "#bloglo-copyright.contained-separator > .bloglo-container:before { background-color: " +
        copyright_separator_color +
        "; }";
      style_css +=
        "#bloglo-copyright.fw-separator { border-top-color: " +
        copyright_separator_color +
        "; }";

      $style_tag.html(style_css);
    });
  });

  /**
   * Footer border.
   */
  api("bloglo_footer_border", function (value) {
    value.bind(function (newval) {
      var $footer = $("#bloglo-footer");

      if (!$footer.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_footer_border");
      var style_css = "";

      if (newval["border-top-width"]) {
        style_css +=
          "#colophon { " +
          "border-top-width: " +
          newval["border-top-width"] +
          "px;" +
          "border-top-style: " +
          newval["border-style"] +
          ";" +
          "border-top-color: " +
          newval["border-color"] +
          ";" +
          "}";
      }

      if (newval["border-bottom-width"]) {
        style_css +=
          "#colophon { " +
          "border-bottom-width: " +
          newval["border-bottom-width"] +
          "px;" +
          "border-bottom-style: " +
          newval["border-style"] +
          ";" +
          "border-bottom-color: " +
          newval["border-color"] +
          ";" +
          "}";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Copyright layout.
   */
  api("bloglo_copyright_layout", function (value) {
    value.bind(function (newval) {
      $body.removeClass(function (index, className) {
        return (
          className.match(/(^|\s)bloglo-copyright-layout-\S+/g) || []
        ).join(" ");
      });

      $body.addClass("bloglo-copyright-" + newval);
    });
  });

  /**
   * Copyright separator.
   */
  api("bloglo_copyright_separator", function (value) {
    value.bind(function (newval) {
      var $copyright = $("#bloglo-copyright");

      if (!$copyright.length) {
        return;
      }

      $copyright.removeClass("fw-separator contained-separator");

      if ("none" !== newval) {
        $copyright.addClass(newval);
      }
    });
  });

  /**
   * Copyright visibility.
   */
  api("bloglo_copyright_visibility", function (value) {
    value.bind(function (newval) {
      bloglo_print_visibility_classes($("#bloglo-copyright"), newval);
    });
  });

  /**
   * Copyright background.
   */
  api("bloglo_copyright_background", function (value) {
    value.bind(function (newval) {
      var $copyright = $("#bloglo-copyright");

      if (!$copyright.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_copyright_background");
      var style_css = bloglo_design_options_css(
        "#bloglo-copyright",
        newval,
        "background"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Copyright text color.
   */
  api("bloglo_copyright_text_color", function (value) {
    value.bind(function (newval) {
      var $copyright = $("#bloglo-copyright");

      if (!$copyright.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_copyright_text_color");
      var style_css = "";

      newval["text-color"] = newval["text-color"]
        ? newval["text-color"]
        : "inherit";
      newval["link-color"] = newval["link-color"]
        ? newval["link-color"]
        : "inherit";
      newval["link-hover-color"] = newval["link-hover-color"]
        ? newval["link-hover-color"]
        : "inherit";

      // Text color.
      style_css += "#bloglo-copyright { color: " + newval["text-color"] + "; }";

      // Link color.
      style_css +=
        "#bloglo-copyright a { color: " + newval["link-color"] + "; }";

      // Link hover color.
      style_css +=
        "#bloglo-copyright a:hover, #bloglo-copyright .bloglo-social-nav > ul > li > a .bloglo-icon.bottom-icon, #bloglo-copyright li.current_page_item > a, #bloglo-copyright .bloglo-nav > ul > li.current-menu-item > a, #bloglo-copyright .bloglo-nav > ul > li.current-menu-ancestor > a #bloglo-copyright .bloglo-nav > ul > li:hover > a, #bloglo-copyright .bloglo-social-nav > ul > li > a .bloglo-icon.bottom-icon { color: " +
        newval["link-hover-color"] +
        "; }";

      $style_tag.html(style_css);
    });
  });

  /**
   * Container width.
   */
  api("bloglo_container_width", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_container_width");
      var style_css;

      style_css =
        ".bloglo-container," +
        ".alignfull > div { " +
        "max-width: " +
        newval.value +
        "px;" +
        "}";

      style_css +=
        ".bloglo-layout__boxed #page, .bloglo-layout__boxed.bloglo-sticky-header.bloglo-is-mobile #bloglo-header-inner, " +
        ".bloglo-layout__boxed.bloglo-sticky-header:not(.bloglo-header-layout-3) #bloglo-header-inner, " +
        ".bloglo-layout__boxed.bloglo-sticky-header:not(.bloglo-is-mobile).bloglo-header-layout-3 #bloglo-header-inner .bloglo-nav-container > .bloglo-container { max-width: " +
        (parseInt(newval.value) + 100) +
        "px; }";

      $style_tag.html(style_css);
    });
  });

  /**
   * Transparent Header Logo max height.
   */
  api("bloglo_tsp_logo_max_height", function (value) {
    value.bind(function (newval) {
      var $logo = $(".bloglo-tsp-header .bloglo-logo");

      if (!$logo.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_tsp_logo_max_height");
      var style_css = "";

      style_css += bloglo_range_field_css(
        ".bloglo-tsp-header .bloglo-logo img",
        "max-height",
        newval,
        true,
        "px"
      );
      style_css += bloglo_range_field_css(
        ".bloglo-tsp-header .bloglo-logo img.bloglo-svg-logo",
        "height",
        newval,
        true,
        "px"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Transparent Header Logo margin.
   */
  api("bloglo_tsp_logo_margin", function (value) {
    value.bind(function (newval) {
      var $logo = $(".bloglo-tsp-header .bloglo-logo");

      if (!$logo.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_tsp_logo_margin");

      var style_css = bloglo_spacing_field_css(
        ".bloglo-tsp-header .bloglo-logo .logo-inner",
        "margin",
        newval,
        true
      );
      $style_tag.html(style_css);
    });
  });

  /**
   * Transparent header - Main Header & Topbar background.
   */
  api("bloglo_tsp_header_background", function (value) {
    value.bind(function (newval) {
      var $tsp_header = $(".bloglo-tsp-header");

      if (!$tsp_header.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_tsp_header_background");

      var style_css = "";
      style_css += bloglo_design_options_css(
        ".bloglo-tsp-header #bloglo-header-inner",
        newval,
        "background"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Transparent header - Main Header & Topbar font color.
   */
  api("bloglo_tsp_header_font_color", function (value) {
    value.bind(function (newval) {
      var $tsp_header = $(".bloglo-tsp-header");

      if (!$tsp_header.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_tsp_header_font_color");

      var style_css = "";

      newval["text-color"] = newval["text-color"]
        ? newval["text-color"]
        : "inherit";
      newval["link-color"] = newval["link-color"]
        ? newval["link-color"]
        : "inherit";
      newval["link-hover-color"] = newval["link-hover-color"]
        ? newval["link-hover-color"]
        : "inherit";

      /** Header **/

      // Text color.
      style_css +=
        ".bloglo-tsp-header .bloglo-logo .site-description { color: " +
        newval["text-color"] +
        "; }";

      // Link color.
      if (newval["link-color"]) {
        style_css +=
          ".bloglo-tsp-header #bloglo-header, " +
          ".bloglo-tsp-header .bloglo-header-widgets a:not(.bloglo-btn), " +
          ".bloglo-tsp-header .bloglo-logo a," +
          ".bloglo-tsp-header .bloglo-hamburger, " +
          ".bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li > a { color: " +
          newval["link-color"] +
          "; }";
        style_css +=
          ".bloglo-tsp-header .hamburger-inner," +
          ".bloglo-tsp-header .hamburger-inner::before," +
          ".bloglo-tsp-header .hamburger-inner::after { background-color: " +
          newval["link-color"] +
          "; }";
      }

      // Link hover color.
      if (newval["link-hover-color"]) {
        style_css +=
          ".bloglo-tsp-header .bloglo-header-widgets a:not(.bloglo-btn):hover, " +
          ".bloglo-tsp-header #bloglo-header-inner .bloglo-header-widgets .bloglo-active," +
          ".bloglo-tsp-header .bloglo-logo .site-title a:hover, " +
          ".bloglo-tsp-header .bloglo-hamburger:hover .hamburger-label, " +
          ".is-mobile-menu-active .bloglo-tsp-header .bloglo-hamburger .hamburger-label," +
          ".bloglo-tsp-header.using-keyboard .site-title a:focus," +
          ".bloglo-tsp-header.using-keyboard .bloglo-header-widgets a:not(.bloglo-btn):focus," +
          ".bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li.hovered > a," +
          ".bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li > a:hover," +
          ".bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li.menu-item-has-children:hover > a," +
          ".bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li.current-menu-item > a," +
          ".bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li.current-menu-ancestor > a," +
          ".bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li.page_item_has_children:hover > a," +
          ".bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li.current_page_item > a," +
          ".bloglo-tsp-header #bloglo-header-inner .bloglo-nav > ul > li.current_page_ancestor > a { color: " +
          newval["link-hover-color"] +
          "; }";

        style_css +=
          ".bloglo-tsp-header .bloglo-hamburger:hover .hamburger-inner," +
          ".bloglo-tsp-header .bloglo-hamburger:hover .hamburger-inner::before," +
          ".bloglo-tsp-header .bloglo-hamburger:hover .hamburger-inner::after," +
          ".is-mobile-menu-active .bloglo-tsp-header .bloglo-hamburger .hamburger-inner," +
          ".is-mobile-menu-active .bloglo-tsp-header .bloglo-hamburger .hamburger-inner::before," +
          ".is-mobile-menu-active .bloglo-tsp-header .bloglo-hamburger .hamburger-inner::after { background-color: " +
          newval["link-hover-color"] +
          "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Transparent header - Main Header & Topbar border.
   */
  api("bloglo_tsp_header_border", function (value) {
    value.bind(function (newval) {
      var $tsp_header = $(".bloglo-tsp-header");

      if (!$tsp_header.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_tsp_header_border");

      var style_css = "";

      style_css += bloglo_design_options_css(
        ".bloglo-tsp-header #bloglo-header-inner",
        newval,
        "border"
      );

      // Separator color.
      newval["separator-color"] = newval["separator-color"]
        ? newval["separator-color"]
        : "inherit";
      style_css +=
        ".bloglo-tsp-header .bloglo-header-widget:after { background-color: " +
        newval["separator-color"] +
        "; }";

      $style_tag.html(style_css);
    });
  });

  /**
   * Page Header layout.
   */
  api("bloglo_page_header_alignment", function (value) {
    value.bind(function (newval) {
      if ($body.hasClass("single-post")) {
        return;
      }

      $body.removeClass(function (index, className) {
        return (
          className.match(/(^|\s)bloglo-page-title-align-\S+/g) || []
        ).join(" ");
      });

      $body.addClass("bloglo-page-title-align-" + newval);
    });
  });

  /**
   * Page Header spacing.
   */
  api("bloglo_page_header_spacing", function (value) {
    value.bind(function (newval) {
      var $page_header = $(".page-header");

      if (!$page_header.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_page_header_spacing");

      var style_css = bloglo_spacing_field_css(
        ".bloglo-page-title-align-left .page-header.bloglo-has-page-title, .bloglo-page-title-align-right .page-header.bloglo-has-page-title, .bloglo-page-title-align-center .page-header .bloglo-page-header-wrapper",
        "padding",
        newval,
        true
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Page Header background.
   */
  api("bloglo_page_header_background", function (value) {
    value.bind(function (newval) {
      var $page_header = $(".page-header");

      if (!$page_header.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_page_header_background");

      var style_css = "";
      style_css += bloglo_design_options_css(
        ".page-header",
        newval,
        "background"
      );
      style_css += bloglo_design_options_css(
        ".bloglo-tsp-header:not(.bloglo-tsp-absolute) #masthead",
        newval,
        "background"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Header Text color.
   */
  api("bloglo_page_header_text_color", function (value) {
    value.bind(function (newval) {
      var $page_header = $(".page-header");

      if (!$page_header.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_page_header_text_color");
      var style_css = "";

      newval["text-color"] = newval["text-color"]
        ? newval["text-color"]
        : "inherit";
      newval["link-color"] = newval["link-color"]
        ? newval["link-color"]
        : "inherit";
      newval["link-hover-color"] = newval["link-hover-color"]
        ? newval["link-hover-color"]
        : "inherit";

      // Text color.
      style_css +=
        ".page-header .page-title { color: " + newval["text-color"] + "; }";
      style_css +=
        ".page-header .bloglo-page-header-description" +
        "{ color: " +
        bloglo_hex2rgba(newval["text-color"], 0.75) +
        "}";

      // Link color.
      style_css +=
        ".page-header .bloglo-breadcrumbs a" +
        "{ color: " +
        newval["link-color"] +
        "; }";

      style_css +=
        ".page-header .bloglo-breadcrumbs span," +
        ".page-header .breadcrumb-trail .trail-items li::after, .page-header .bloglo-breadcrumbs .separator" +
        "{ color: " +
        bloglo_hex2rgba(newval["link-color"], 0.75) +
        "}";

      $style_tag.html(style_css);
    });
  });

  /**
   * Page Header border.
   */
  api("bloglo_page_header_border", function (value) {
    value.bind(function (newval) {
      var $page_header = $(".page-header");

      if (!$page_header.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_page_header_border");
      var style_css = bloglo_design_options_css(
        ".page-header",
        newval,
        "border"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Breadcrumbs alignment.
   */
  api("bloglo_breadcrumbs_alignment", function (value) {
    value.bind(function (newval) {
      var $breadcrumbs = $("#main > .bloglo-breadcrumbs > .bloglo-container");

      if (!$breadcrumbs.length) {
        return;
      }

      $breadcrumbs.removeClass(function (index, className) {
        return (className.match(/(^|\s)bloglo-text-align\S+/g) || []).join(" ");
      });

      $breadcrumbs.addClass("bloglo-text-align-" + newval);
    });
  });

  /**
   * Breadcrumbs spacing.
   */
  api("bloglo_breadcrumbs_spacing", function (value) {
    value.bind(function (newval) {
      var $breadcrumbs = $(".bloglo-breadcrumbs");

      if (!$breadcrumbs.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_breadcrumbs_spacing");

      var style_css = bloglo_spacing_field_css(
        ".bloglo-breadcrumbs",
        "padding",
        newval,
        true
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Breadcrumbs Background.
   */
  api("bloglo_breadcrumbs_background", function (value) {
    value.bind(function (newval) {
      var $breadcrumbs = $(".bloglo-breadcrumbs");

      if (!$breadcrumbs.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_breadcrumbs_background");
      var style_css = bloglo_design_options_css(
        ".bloglo-breadcrumbs",
        newval,
        "background"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Breadcrumbs Text Color.
   */
  api("bloglo_breadcrumbs_text_color", function (value) {
    value.bind(function (newval) {
      var $breadcrumbs = $(".bloglo-breadcrumbs");

      if (!$breadcrumbs.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_breadcrumbs_text_color");
      var style_css = bloglo_design_options_css(
        ".bloglo-breadcrumbs",
        newval,
        "color"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Breadcrumbs Border.
   */
  api("bloglo_breadcrumbs_border", function (value) {
    value.bind(function (newval) {
      var $breadcrumbs = $(".bloglo-breadcrumbs");

      if (!$breadcrumbs.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_breadcrumbs_border");
      var style_css = bloglo_design_options_css(
        ".bloglo-breadcrumbs",
        newval,
        "border"
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Base HTML font size.
   */
  api("bloglo_html_base_font_size", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_html_base_font_size");
      var style_css = bloglo_range_field_css(
        "html",
        "font-size",
        newval,
        true,
        "%"
      );
      $style_tag.html(style_css);
    });
  });

  /**
   * Font smoothing.
   */
  api("bloglo_font_smoothing", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_font_smoothing");

      if (newval) {
        $style_tag.html(
          "*," +
            "*::before," +
            "*::after {" +
            "-moz-osx-font-smoothing: grayscale;" +
            "-webkit-font-smoothing: antialiased;" +
            "}"
        );
      } else {
        $style_tag.html(
          "*," +
            "*::before," +
            "*::after {" +
            "-moz-osx-font-smoothing: auto;" +
            "-webkit-font-smoothing: auto;" +
            "}"
        );
      }

      $style_tag = bloglo_get_style_tag("bloglo_html_base_font_size");
      var style_css = bloglo_range_field_css(
        "html",
        "font-size",
        newval,
        true,
        "%"
      );
      $style_tag.html(style_css);
    });
  });

  /**
   * Body font.
   */
  api("bloglo_body_font", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_body_font");
      var style_css = bloglo_typography_field_css("body", newval);

      bloglo_enqueue_google_font(newval["font-family"]);

      $style_tag.html(style_css);
    });
  });

  /**
   * Headings font.
   */
  api("bloglo_headings_font", function (value) {
    var style_css, selector;
    value.bind(function (newval) {
      selector =
        "h1, .h1, .bloglo-logo .site-title, .page-header h1.page-title";
      selector += ", h2, .h2, .woocommerce div.product h1.product_title";
      selector += ", h3, .h3, .woocommerce #reviews #comments h2";
      selector +=
        ", h4, .h4, .woocommerce .cart_totals h2, .woocommerce .cross-sells > h4, .woocommerce #reviews #respond .comment-reply-title";
      selector += ", h5, h6, .h5, .h6";

      style_css = bloglo_typography_field_css(selector, newval);

      bloglo_enqueue_google_font(newval["font-family"]);

      $style_tag = bloglo_get_style_tag("bloglo_headings_font");
      $style_tag.html(style_css);
    });
  });

  /**
   * Heading 1 font.
   */
  api("bloglo_h1_font", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_h1_font");

      var style_css = bloglo_typography_field_css(
        "h1, .h1, .bloglo-logo .site-title, .page-header h1.page-title",
        newval
      );

      bloglo_enqueue_google_font(newval["font-family"]);

      $style_tag.html(style_css);
    });
  });

  /**
   * Heading 2 font.
   */
  api("bloglo_h2_font", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_h2_font");

      var style_css = bloglo_typography_field_css(
        "h2, .h2, .woocommerce div.product h1.product_title",
        newval
      );

      bloglo_enqueue_google_font(newval["font-family"]);

      $style_tag.html(style_css);
    });
  });

  /**
   * Heading 3 font.
   */
  api("bloglo_h3_font", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_h3_font");

      var style_css = bloglo_typography_field_css(
        "h3, .h3, .woocommerce #reviews #comments h2",
        newval
      );

      bloglo_enqueue_google_font(newval["font-family"]);

      $style_tag.html(style_css);
    });
  });

  /**
   * Heading 4 font.
   */
  api("bloglo_h4_font", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_h4_font");

      var style_css = bloglo_typography_field_css(
        "h4, .h4, .woocommerce .cart_totals h2, .woocommerce .cross-sells > h4, .woocommerce #reviews #respond .comment-reply-title",
        newval
      );

      bloglo_enqueue_google_font(newval["font-family"]);

      $style_tag.html(style_css);
    });
  });

  /**
   * Heading 5 font.
   */
  api("bloglo_h5_font", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_h5_font");
      var style_css = bloglo_typography_field_css("h5, .h5", newval);

      bloglo_enqueue_google_font(newval["font-family"]);

      $style_tag.html(style_css);
    });
  });

  /**
   * Heading 6 font.
   */
  api("bloglo_h6_font", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_h6_font");
      var style_css = bloglo_typography_field_css("h6, .h6", newval);

      bloglo_enqueue_google_font(newval["font-family"]);

      $style_tag.html(style_css);
    });
  });

  /**
   * Heading emphasized font.
   */
  api("bloglo_heading_em_font", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_heading_em_font");
      var style_css = bloglo_typography_field_css(
        "h1 em, h2 em, h3 em, h4 em, h5 em, h6 em, .h1 em, .h2 em, .h3 em, .h4 em, .h5 em, .h6 em, .bloglo-logo .site-title em, .error-404 .page-header h1 em",
        newval
      );

      bloglo_enqueue_google_font(newval["font-family"]);

      $style_tag.html(style_css);
    });
  });

  /**
   * Sidebar widget title font size.
   */
  api("bloglo_sidebar_widget_title_font_size", function (value) {
    value.bind(function (newval) {
      var $widget_title = $(
        "#main .widget-title, .widget-area .wp-block-heading"
      );

      if (!$widget_title.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag(
        "bloglo_sidebar_widget_title_font_size"
      );
      var style_css = "";

      style_css += bloglo_range_field_css(
        "#main .widget-title, .widget-area .wp-block-heading",
        "font-size",
        newval,
        true,
        newval.unit
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Footer widget title font size.
   */
  api("bloglo_footer_widget_title_font_size", function (value) {
    value.bind(function (newval) {
      var $widget_title = $(
        "#colophon .widget-title, #colophon .wp-block-heading"
      );

      if (!$widget_title.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_footer_widget_title_font_size");
      var style_css = "";

      style_css += bloglo_range_field_css(
        "#colophon .widget-title, #colophon .wp-block-heading",
        "font-size",
        newval,
        true,
        newval.unit
      );

      $style_tag.html(style_css);
    });
  });

  /**
   * Page title font size.
   */
  api("bloglo_page_header_font_size", function (value) {
    value.bind(function (newval) {
      var $page_title = $(".page-header .page-title");

      if (!$page_title.length) {
        return;
      }

      $style_tag = bloglo_get_style_tag("bloglo_page_header_font_size");
      var style_css = "";

      style_css += bloglo_range_field_css(
        "#page .page-header .page-title",
        "font-size",
        newval,
        true,
        newval.unit
      );

      $style_tag.html(style_css);
    });
  });

  var $btn_selectors =
    ".bloglo-btn, " +
    "body:not(.wp-customizer) input[type=submit], " +
    ".site-main .woocommerce #respond input#submit, " +
    ".site-main .woocommerce a.button, " +
    ".site-main .woocommerce button.button, " +
    ".site-main .woocommerce input.button, " +
    ".woocommerce ul.products li.product .added_to_cart, " +
    ".woocommerce ul.products li.product .button, " +
    ".woocommerce div.product form.cart .button, " +
    ".woocommerce #review_form #respond .form-submit input, " +
    "#infinite-handle span";

  var $btn_hover_selectors =
    ".bloglo-btn:hover, " +
    ".bloglo-btn:focus, " +
    "body:not(.wp-customizer) input[type=submit]:hover, " +
    "body:not(.wp-customizer) input[type=submit]:focus, " +
    ".site-main .woocommerce #respond input#submit:hover, " +
    ".site-main .woocommerce #respond input#submit:focus, " +
    ".site-main .woocommerce a.button:hover, " +
    ".site-main .woocommerce a.button:focus, " +
    ".site-main .woocommerce button.button:hover, " +
    ".site-main .woocommerce button.button:focus, " +
    ".site-main .woocommerce input.button:hover, " +
    ".site-main .woocommerce input.button:focus, " +
    ".woocommerce ul.products li.product .added_to_cart:hover, " +
    ".woocommerce ul.products li.product .added_to_cart:focus, " +
    ".woocommerce ul.products li.product .button:hover, " +
    ".woocommerce ul.products li.product .button:focus, " +
    ".woocommerce div.product form.cart .button:hover, " +
    ".woocommerce div.product form.cart .button:focus, " +
    ".woocommerce #review_form #respond .form-submit input:hover, " +
    ".woocommerce #review_form #respond .form-submit input:focus, " +
    "#infinite-handle span:hover";

  /**
   * Primary button background color.
   */
  api("bloglo_primary_button_bg_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_primary_button_bg_color");
      var style_css = "";

      if (newval) {
        style_css = $btn_selectors + "{ background-color: " + newval + "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Primary button hover background color.
   */
  api("bloglo_primary_button_hover_bg_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_primary_button_hover_bg_color");
      var style_css = "";

      if (newval) {
        style_css =
          $btn_hover_selectors + " { background-color: " + newval + "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Primary button text color.
   */
  api("bloglo_primary_button_text_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_primary_button_text_color");
      var style_css = "";

      if (newval) {
        style_css = $btn_selectors + " { color: " + newval + "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Primary button hover text color.
   */
  api("bloglo_primary_button_hover_text_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag(
        "bloglo_primary_button_hover_text_color"
      );
      var style_css = "";

      if (newval) {
        style_css = $btn_hover_selectors + " { color: " + newval + "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Primary button border width.
   */
  api("bloglo_primary_button_border_width", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_primary_button_border_width");
      var style_css = "";

      if (newval) {
        style_css =
          $btn_selectors + " { border-width: " + newval.value + "rem; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Primary button border radius.
   */
  api("bloglo_primary_button_border_radius", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_primary_button_border_radius");
      var style_css = "";

      if (newval) {
        style_css =
          $btn_selectors +
          " { " +
          "border-top-left-radius: " +
          newval["top-left"] +
          "rem;" +
          "border-top-right-radius: " +
          newval["top-right"] +
          "rem;" +
          "border-bottom-left-radius: " +
          newval["bottom-left"] +
          "rem;" +
          "border-bottom-right-radius: " +
          newval["bottom-right"] +
          "rem; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Primary button border color.
   */
  api("bloglo_primary_button_border_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_primary_button_border_color");
      var style_css = "";

      if (newval) {
        style_css = $btn_selectors + " { border-color: " + newval + "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Primary button hover border color.
   */
  api("bloglo_primary_button_hover_border_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag(
        "bloglo_primary_button_hover_border_color"
      );
      var style_css = "";

      if (newval) {
        style_css = $btn_hover_selectors + " { border-color: " + newval + "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Primary button typography.
   */
  api("bloglo_primary_button_typography", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_primary_button_typography");
      var style_css = bloglo_typography_field_css($btn_selectors, newval);

      bloglo_enqueue_google_font(newval["font-family"]);

      $style_tag.html(style_css);
    });
  });

  // Secondary button.
  var $btn_sec_selectors = ".btn-secondary, .bloglo-btn.btn-secondary";

  var $btn_sec_hover_selectors =
    ".btn-secondary:hover, " +
    ".btn-secondary:focus, " +
    ".bloglo-btn.btn-secondary:hover, " +
    ".bloglo-btn.btn-secondary:focus";

  /**
   * Secondary button background color.
   */
  api("bloglo_secondary_button_bg_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_secondary_button_bg_color");
      var style_css = "";

      if (newval) {
        style_css =
          $btn_sec_selectors + "{ background-color: " + newval + "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Secondary button hover background color.
   */
  api("bloglo_secondary_button_hover_bg_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag(
        "bloglo_secondary_button_hover_bg_color"
      );
      var style_css = "";

      if (newval) {
        style_css =
          $btn_sec_hover_selectors + "{ background-color: " + newval + "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Secondary button text color.
   */
  api("bloglo_secondary_button_text_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_secondary_button_text_color");
      var style_css = "";

      if (newval) {
        style_css = $btn_sec_selectors + "{ color: " + newval + "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Secondary button hover text color.
   */
  api("bloglo_secondary_button_hover_text_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag(
        "bloglo_secondary_button_hover_text_color"
      );
      var style_css = "";

      if (newval) {
        style_css = $btn_sec_hover_selectors + "{ color: " + newval + "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Secondary button border width.
   */
  api("bloglo_secondary_button_border_width", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_secondary_button_border_width");
      var style_css = "";

      if (newval) {
        style_css =
          $btn_sec_selectors + " { border-width: " + newval.value + "rem; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Secondary button border radius.
   */
  api("bloglo_secondary_button_border_radius", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag(
        "bloglo_secondary_button_border_radius"
      );
      var style_css = "";

      if (newval) {
        style_css =
          $btn_sec_selectors +
          " { " +
          "border-top-left-radius: " +
          newval["top-left"] +
          "rem;" +
          "border-top-right-radius: " +
          newval["top-right"] +
          "rem;" +
          "border-bottom-left-radius: " +
          newval["bottom-left"] +
          "rem;" +
          "border-bottom-right-radius: " +
          newval["bottom-right"] +
          "rem; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Secondary button border color.
   */
  api("bloglo_secondary_button_border_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_secondary_button_border_color");
      var style_css = "";

      if (newval) {
        style_css = $btn_sec_selectors + " { border-color: " + newval + "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Secondary button hover border color.
   */
  api("bloglo_secondary_button_hover_border_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag(
        "bloglo_secondary_button_hover_border_color"
      );
      var style_css = "";

      if (newval) {
        style_css =
          $btn_sec_hover_selectors + " { border-color: " + newval + "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Secondary button typography.
   */
  api("bloglo_secondary_button_typography", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_secondary_button_typography");
      var style_css = bloglo_typography_field_css($btn_sec_selectors, newval);

      bloglo_enqueue_google_font(newval["font-family"]);

      $style_tag.html(style_css);
    });
  });

  // Text button.
  var $btn_text_selectors = ".bloglo-btn.btn-text-1, .btn-text-1";

  var $btn_text_hover_selectors =
    ".bloglo-btn.btn-text-1:hover, .bloglo-btn.btn-text-1:focus, .btn-text-1:hover, .btn-text-1:focus";

  /**
   * Text button text color.
   */
  api("bloglo_text_button_text_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_text_button_text_color");
      var style_css = "";

      if (newval) {
        style_css = $btn_text_selectors + "{ color: " + newval + "; }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Text button hover text color.
   */
  api("bloglo_text_button_hover_text_color", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_text_button_hover_text_color");
      var style_css = "";

      if (newval) {
        style_css = $btn_text_hover_selectors + "{ color: " + newval + "; }";
        style_css +=
          ".bloglo-btn.btn-text-1 > span::before { background-color: " +
          newval +
          " }";
      }

      $style_tag.html(style_css);
    });
  });

  /**
   * Text button typography.
   */
  api("bloglo_text_button_typography", function (value) {
    value.bind(function (newval) {
      $style_tag = bloglo_get_style_tag("bloglo_text_button_typography");
      var style_css = bloglo_typography_field_css($btn_text_selectors, newval);

      bloglo_enqueue_google_font(newval["font-family"]);

      $style_tag.html(style_css);
    });
  });

  /**
   * Section Heading Style Enable.
   */
  api("bloglo_section_heading_style", function (value) {
    value.bind(function (newval) {
      $body
        .removeClass(function (index, className) {
          return (
            className.match(/(^|\s)is-section-heading-init-s\S+/g) || []
          ).join(" ");
        })
        .addClass(
          "is-section-heading-init-s" +
            api.value("bloglo_section_heading_style")()
        );
    });
  });

  // Selective refresh.
  if (api.selectiveRefresh) {
    // Bind partial content rendered event.
    api.selectiveRefresh.bind("partial-content-rendered", function (placement) {
      // Hero Hover Slider.
      if (
        "bloglo_hero_hover_slider_post_number" === placement.partial.id ||
        "bloglo_hero_hover_slider_elements" === placement.partial.id
      ) {
        document
          .querySelectorAll(placement.partial.params.selector)
          .forEach((item) => {
            blogloHoverSlider(item);
          });

        // Force refresh height.
        api("bloglo_hero_hover_slider_height", function (newval) {
          newval.callbacks.fireWith(newval, [newval.get()]);
        });
      }

      // Preloader style.
      if ("bloglo_preloader_style" === placement.partial.id) {
        $body.removeClass("bloglo-loaded");

        setTimeout(function () {
          window.bloglo.preloader();
        }, 300);
      }
    });
  }

  // Custom Customizer Preview class (attached to the Customize API)
  api.blogloCustomizerPreview = {
    // Init
    init: function () {
      var self = this; // Store a reference to "this"
      var previewBody = self.preview.body;

      previewBody.on("click", ".bloglo-set-widget", function () {
        self.preview.send("set-footer-widget", $(this).data("sidebar-id"));
      });
    },
  };

  /**
   * Capture the instance of the Preview since it is private (this has changed in WordPress 4.0)
   *
   * @see https://github.com/WordPress/WordPress/blob/5cab03ab29e6172a8473eb601203c9d3d8802f17/wp-admin/js/customize-controls.js#L1013
   */
  var blogloOldPreview = api.Preview;
  api.Preview = blogloOldPreview.extend({
    initialize: function (params, options) {
      // Store a reference to the Preview
      api.blogloCustomizerPreview.preview = this;

      // Call the old Preview's initialize function
      blogloOldPreview.prototype.initialize.call(this, params, options);
    },
  });

  // Document ready
  $(function () {
    // Initialize our Preview
    api.blogloCustomizerPreview.init();
  });
})(jQuery);
