'use strict';

class Feature {
  constructor() {
    this._initFeature();
  }

  _initFeature() {
    jQuery('.click-icon-wrapper .dropdown-menu').on('click', function (event) {
      event.stopPropagation();
    });
  }

}

jQuery(document).ready(() => {
  new Feature();
});
