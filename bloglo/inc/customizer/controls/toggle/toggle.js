// Toggle control
wp.customize.controlConstructor["bloglo-toggle"] = wp.customize.Control.extend({
  ready: function () {
    "use strict";

    var control = this;

    // Change the value
    control.container.on("click", ".bloglo-toggle-switch", function () {
      control.setting.set(!control.setting.get());
    });
  },
});
