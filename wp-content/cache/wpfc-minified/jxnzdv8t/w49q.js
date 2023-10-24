// source --> https://clickcollect.chickenspot.com/wp-admin/js/color-picker.min.js?ver=3.0.2 
/*! This file is auto-generated */
!function(i,t){var a=wp.i18n.__;i.widget("wp.wpColorPicker",{options:{defaultColor:!1,change:!1,clear:!1,hide:!0,palettes:!0,width:255,mode:"hsv",type:"full",slider:"horizontal"},_createHueOnly:function(){var e,o=this,t=o.element;t.hide(),e="hsl("+t.val()+", 100, 50)",t.iris({mode:"hsl",type:"hue",hide:!1,color:e,change:function(e,t){"function"==typeof o.options.change&&o.options.change.call(this,e,t)},width:o.options.width,slider:o.options.slider})},_create:function(){if(i.support.iris){var o=this,e=o.element;if(i.extend(o.options,e.data()),"hue"===o.options.type)return o._createHueOnly();o.close=o.close.bind(o),o.initialValue=e.val(),e.addClass("wp-color-picker"),e.parent("label").length||(e.wrap("<label></label>"),o.wrappingLabelText=i('<span class="screen-reader-text"></span>').insertBefore(e).text(a("Color value"))),o.wrappingLabel=e.parent(),o.wrappingLabel.wrap('<div class="wp-picker-container" />'),o.wrap=o.wrappingLabel.parent(),o.toggler=i('<button type="button" class="button wp-color-result" aria-expanded="false"><span class="wp-color-result-text"></span></button>').insertBefore(o.wrappingLabel).css({backgroundColor:o.initialValue}),o.toggler.find(".wp-color-result-text").text(a("Select Color")),o.pickerContainer=i('<div class="wp-picker-holder" />').insertAfter(o.wrappingLabel),o.button=i('<input type="button" class="button button-small" />'),o.options.defaultColor?o.button.addClass("wp-picker-default").val(a("Default")).attr("aria-label",a("Select default color")):o.button.addClass("wp-picker-clear").val(a("Clear")).attr("aria-label",a("Clear color")),o.wrappingLabel.wrap('<span class="wp-picker-input-wrap hidden" />').after(o.button),o.inputWrapper=e.closest(".wp-picker-input-wrap"),e.iris({target:o.pickerContainer,hide:o.options.hide,width:o.options.width,mode:o.options.mode,palettes:o.options.palettes,change:function(e,t){o.toggler.css({backgroundColor:t.color.toString()}),"function"==typeof o.options.change&&o.options.change.call(this,e,t)}}),e.val(o.initialValue),o._addListeners(),o.options.hide||o.toggler.click()}},_addListeners:function(){var o=this;o.wrap.on("click.wpcolorpicker",function(e){e.stopPropagation()}),o.toggler.on("click",function(){o.toggler.hasClass("wp-picker-open")?o.close():o.open()}),o.element.on("change",function(e){var t=i(this).val();""!==t&&"#"!==t||(o.toggler.css("backgroundColor",""),"function"==typeof o.options.clear&&o.options.clear.call(this,e))}),o.button.on("click",function(e){var t=i(this);t.hasClass("wp-picker-clear")?(o.element.val(""),o.toggler.css("backgroundColor",""),"function"==typeof o.options.clear&&o.options.clear.call(this,e)):t.hasClass("wp-picker-default")&&o.element.val(o.options.defaultColor).change()})},open:function(){this.element.iris("toggle"),this.inputWrapper.removeClass("hidden"),this.wrap.addClass("wp-picker-active"),this.toggler.addClass("wp-picker-open").attr("aria-expanded","true"),i("body").trigger("click.wpcolorpicker").on("click.wpcolorpicker",this.close)},close:function(){this.element.iris("toggle"),this.inputWrapper.addClass("hidden"),this.wrap.removeClass("wp-picker-active"),this.toggler.removeClass("wp-picker-open").attr("aria-expanded","false"),i("body").off("click.wpcolorpicker",this.close)},color:function(e){if(e===t)return this.element.iris("option","color");this.element.iris("option","color",e)},defaultColor:function(e){if(e===t)return this.options.defaultColor;this.options.defaultColor=e}})}(jQuery);
// source --> https://clickcollect.chickenspot.com/wp-content/themes/fast-food-child/assets/js/scripts.js?ver=6.3.1 
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function csSetCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
  let expires = "expires=" + d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}


jQuery(document).ready(function ($) {
  var activeIndex = 0;
  var lastAtiveInext = 0;
  var totalIndex = $("li.cs-cat-list-item").length - 4;


  

  jQuery(window).on("scroll", function () {
    var sTop = jQuery(window).scrollTop();
    jQuery(".cs-products-category").each(function () {
      var id = jQuery(this).attr("data-cat_id"),
        offset = jQuery(this).offset().top - 180,
        height = jQuery(this).height();
      if (sTop >= offset && sTop < offset + height) {
        jQuery(".cs-cat-list-item ").removeClass("cs-active");
        jQuery(".cs-cat-list")
          .find('[data-id="' + id + '"]')
          .addClass("cs-active");
        activeIndex = jQuery(".cs-cat-list")
          .find('[data-id="' + id + '"]')
          .index();
      }
    });
    if (activeIndex !== lastAtiveInext) {
      //console.log(activeIndex, lastAtiveInext)
      if (activeIndex > lastAtiveInext && activeIndex >= 2) {
        //console.log("User is scrolling down");
        $(".right-btn-menu").trigger("click");
      } else if (totalIndex >= activeIndex) {
        //console.log("User is scrolling up");
        $(".left-btn-menu").trigger("click");
      }
      lastAtiveInext = activeIndex;
    }
  });

 

  $("#password_2").keyup(function () {
    var password = $("#password_1").val();
    var confirmPassword = $(this).val();
    if (password != confirmPassword) {
      $("#error-message").text("Passwords do not match");
    } else {
      $("#error-message").text("");
    }
  });

    // const errorDiv = document.getElementById("error-message");

    // Get references to the password fields and submit button
    var passwordField = $("#password_1");
    var confirmPasswordField = $("#password_2");
    var submitButton = $(".last_submit");

    // Attach event listeners to the password fields
    passwordField.on("input", checkPasswords);
    confirmPasswordField.on("input", checkPasswords);

    // Function to check if the passwords match and update the UI accordingly
    function checkPasswords() {
      var password = passwordField.val();
      var confirmPassword = confirmPasswordField.val();

      // If the passwords match
      if (password === confirmPassword) {
        $("#error-message").text("");
        confirmPasswordField.removeClass("invalid");
        submitButton.prop("disabled", false);
      }
      // If the passwords don't match
      else {
        $("#error-message").text("Passwords do not match");
        confirmPasswordField.addClass("invalid");
        submitButton.prop("disabled", true);
      }
    }

  if (
    !$(
      ".single-product .yith-wapo-addon-type-checkbox:nth-child(2) .options .selection-multiple"
    ).length
  ) {
    $(".slideIcon").css("display", "none");
  } else {
    $(".slideIcon").css("display", "block");
  }

  var slideWidth = $(
    ".single-product .yith-wapo-addon-type-checkbox:first-child .options .selection-multiple"
  ).outerWidth(true);
  var slideCount = $(
    ".single-product .yith-wapo-addon-type-checkbox:first-child .options .selection-multiple"
  ).length;
  var slideWrapperWidth = slideWidth * slideCount;
  $(".single-product .yith-wapo-addon-type-checkbox:first-child .options").css(
    "width",
    slideWrapperWidth
  );
  var currentSlide = 1;
  var maxSlides = slideCount - 3;

  $(".prev-btn-ch-first").click(function () {
    if (currentSlide > 1) {
      currentSlide--;
      $(
        ".single-product .yith-wapo-addon-type-checkbox:first-child .options"
      ).css(
        "transform",
        "translateX(" + -slideWidth * (currentSlide - 1) + "px)"
      );
    }
  });

  $(".next-btn-ch-first").click(function () {
    if (currentSlide < maxSlides) {
      currentSlide++;
      $(
        ".single-product .yith-wapo-addon-type-checkbox:first-child .options"
      ).css(
        "transform",
        "translateX(" + -slideWidth * (currentSlide - 1) + "px)"
      );
    }
  });

  //secend slider

  var slideWidth2 = $(
    ".single-product .yith-wapo-addon-type-checkbox:nth-child(2) .options .selection-multiple"
  ).outerWidth(true);
  var slideCount2 = $(
    ".single-product .yith-wapo-addon-type-checkbox:nth-child(2) .options .selection-multiple"
  ).length;
  var slideWrapperWidth2 = slideWidth2 * slideCount2;
  $(".single-product .yith-wapo-addon-type-checkbox:nth-child(2) .options").css(
    "width",
    slideWrapperWidth2
  );
  var currentSlide2 = 1;
  var maxSlides2 = slideCount2 - 3;

  $(".prev-btn-ch-secnd").click(function () {
    if (currentSlide2 > 1) {
      currentSlide2--;
      $(
        ".single-product .yith-wapo-addon-type-checkbox:nth-child(2) .options"
      ).css(
        "transform",
        "translateX(" + -slideWidth2 * (currentSlide2 - 1) + "px)"
      );
    }
  });

  $(".next-btn-ch-secnd").click(function () {
    if (currentSlide2 < maxSlides2) {
      currentSlide2++;
      $(
        ".single-product .yith-wapo-addon-type-checkbox:nth-child(2) .options"
      ).css(
        "transform",
        "translateX(" + -slideWidth2 * (currentSlide2 - 1) + "px)"
      );
    }
  });

  /**Perpetual code */
  $(".add_to_cart_btn").click(function (e) {
    e.preventDefault();
    // Get the id_product value from the data attribute
    var id_product = $(this).attr("data-product_id");
    var ajaxCartUrl = customScriptData.ajaxCartUrl;
    $.ajax({
      url: ajaxCartUrl,
      method: "GET",
      data: {
        action: "get_product_popup_mobile",
        id_product: id_product,
      },
      success: function (response) {
        // Update the modal content with the retrieved product data
        $(".product_popup_content").html(response);

        // Show the modal
        $("#product_popup").fadeIn(300);
      },
      error: function (xhr, status, error) {
        //$spinner.hide().fadeOut(300);
        console.log(error);
      },
    });
  });

  $(".product-plus-button").click(function (e) {
    e.preventDefault();
    // Get the id_product value from the data attribute
    var id_product = $(this).attr("data_product_id");
    var ajaxCartUrl = customScriptData.ajaxCartUrl;
    $.ajax({
      url: ajaxCartUrl,
      method: "GET",
      data: {
        action: "get_product_popup",
        id_product: id_product,
      },
      success: function (response) {
        // Update the modal content with the retrieved product data
        $(".product_popup_content").html(response);

        // Show the modal
        $("#product_popup").fadeIn(300);
      },
      error: function (xhr, status, error) {
        //$spinner.hide().fadeOut(300);
        console.log(error);
      },
    });
  });

  $(".reduce-quantity-button").click(function (e) {
    e.preventDefault();
    // Get the id_product value from the data attribute
    var id_product = $(this).attr("data_product_id");
    var quantity = $(this).attr("data-quantity");
    var ajaxCartUrl = customScriptData.ajaxCartUrl;
    $.ajax({
      url: ajaxCartUrl,
      method: "GET",
      data: {
        action: "reduce_product",
        id_product: id_product,
        quantity: quantity,
      },
      success: function (response) {
        // Update the modal content with the retrieved product data
        console.log(response);
        //$(".product_popup_content").html(response);

        // Show the modal
        $("#product_popup").fadeIn(300);
      },
      error: function (xhr, status, error) {
        //$spinner.hide().fadeOut(300);
        console.log(error);
      },
    });
  });

  $(".product_popup .close").click(function (e) {
    e.preventDefault();
    $(".product_popup").hide();
  });
  $(".modify_modal .close").click(function (e) {
    e.preventDefault();
    $(".modify_modal").hide();
  });

  $(".cancel_btn").magnificPopup({
    type: "inline",
    closeBtnInside: true,
    autoFocusLast: true,
    focus: ".modal-title",
  });

  $("#modal_pickup_cancel .pickup_cancel_btn").click(function (e) {
    e.preventDefault();

    // Get the form data
    var formData = $("#modal_pickup_cancel .pickup_actions").serialize();

    $.ajax({
      url: "/checkout",
      method: "POST",
      data: formData,
      success: function (response) {
        // Trigger click event on .mfp-close button
        $("#modal_pickup_cancel .mfp-close").trigger("click");
        // Reload the page
        location.reload();
      },
      error: function (xhr, status, error) {
        console.error(error);
      },
    });
  });

  /**Perpetual code */

  // toogle eye_icon


  const progress = $(".js-completed-bar");

  if (progress.length > 0) {
    const completePercentage = progress.data("complete");
    progress.css({
      width: completePercentage + "%",
      opacity: 1,
    });
  }

  $(".toggle-button").on("click", function (e) {
    e.preventDefault();

    if ($(".hidden-div").hasClass("out")) {
      setTimeout(function () {
        $(".hidden-div").removeClass("out");
        $(".hidden-div").addClass("active");
        $(".arrow").addClass("active");
      }, 200); // Add a small delay to ensure smooth transition
    } else {
      setTimeout(function () {
        $(".hidden-div").removeClass("active");
        $(".hidden-div").addClass("out");
        $(".arrow").removeClass("active");
      }, 300);
    }

  });

  $(".cs-cat-list-item").click(function (e) {
    e.preventDefault();
    var getCatID = $(this).attr("data-id");
    $(".cs-cat-list-item").removeClass("cs-active");
    $("#cs_cat_" + getCatID).addClass("cs-active");
    var top_ref_no = 60;
    if ($("body").hasClass("admin-bar")) {
      top_ref_no = 120;
    }
    $("html, body").animate(
      { scrollTop: $("#cs_cat_product_" + getCatID).offset().top - top_ref_no },
      600
    );
  });
  $(".cs-menu-btn.left-btn-menu").click(function (e) {
    e.preventDefault();
    $(".cs-cat-list").animate({ scrollLeft: "-=130" }, 400);
  });

  $(".cs-menu-btn.right-btn-menu").click(function (e) {
    e.preventDefault();
    $(".cs-cat-list").animate({ scrollLeft: "+=110" }, 400);
  });

  $("._location_btn").click(function () {
    $("#wcfmmp-stores-wrap .wcfmmp-store-wrap").hide();
    $(".pop-up-modal ").fadeIn(1000);
  });
  $(".cls").click(function (e) {
    e.preventDefault();
    $(".pop-up-modal").hide();
  });

  var timezone_offset_minutes = new Date().getTimezoneOffset();
  timezone_offset_minutes =
    timezone_offset_minutes == 0 ? 0 : -timezone_offset_minutes;
  csSetCookie("cs_timezone", timezone_offset_minutes, 30);

  
  $(".account_login a").click(function () {
    $("._c_register_form").show();
    $("._login_form").hide();
  });
  $(".register_account a").click(function () {
    $("._c_register_form").hide();
    $("._login_form").show();
  });

  $(".register_account .register_form").click(function () {
    $("._c_register_form ").show();
    $("._login_form").hide();
  });

  $("._c_register_form  .login_form").click(function () {
    $("._login_form ").show();
    $("._c_register_form").hide();
  });

  $("body").on("click", "._btn_cart", function (e) {
    // $(".button[name=update_cart]").click();
  });
  $("body").on("click", "._show_product_detail", function () {
    $(this).parent().find("._product_meta").slideToggle();
  });
  /*
  $("body").on("click", "#wcfmmp-stores-wrap .store-wrapper", function () {
    $(this).find(".store-footer").slideToggle();
    $(this).toggleClass("arrow_rotate");
  });
*/
  $("body").on("click", "#close-map", function () {
    //console.log("Hello");
    $(".cs_locator_map").slideToggle();
  });
  $(".add_to_cart").on("click", function (e) {
    $(".single_add_to_cart_button").trigger("click");
  });

  $(".checkout_btn").on("click", function () {
    $(".checkout.woocommerce-checkout").trigger("submit");
  });

  $(".pickup_confirm_btn").unbind('click').bind('click', function (e) {
    e.preventDefault();
    var formData = $(".pickup_actions").serialize();
    var order_id = $(".pickup_actions input[name='order_id']").val();
    $("#loading").show().fadeIn(300);
    var ajaxCartUrl = customScriptData.ajaxCartUrl;
    $.ajax({
      url: ajaxCartUrl,
      method: "GET",
      data: {
        action: "pickup_confirm",
        id_order: order_id,
      },
      success: function (response) {
        $("#loading").hide().fadeOut(300);
        $(".pickup_msg_before").hide(300);
        $(".pickup_msg_after").show(300);
        $(".pickup_msg_info").hide(300);
        $(".pickup_actions").hide(300);
        $(".js-completed-bar.completed-bar").css('background-color', '#000195');
        console.log(response);
      },
      error: function (xhr, status, error) {
        console.log(error);
      },
    });
  });

  $(".pickup_cancel_btn").unbind('click').bind('click', function (e) {
    e.preventDefault();
    var order_id = $(".pickup_actions input[name='order_id']").val();
    var ajaxCartUrl = customScriptData.ajaxCartUrl;
    $.ajax({
      url: ajaxCartUrl,
      method: "GET",
      data: {
        action: "pickup_cancel",
        id_order: order_id,
      },
      success: function (response) {
        window.location.href = response;
      },
      error: function (xhr, status, error) {
        console.log(error);
      },
    });
  });

  $("body").on("click", ".cs_order_as_guest", function (e) {
    e.preventDefault();
    $(".woocommerce-checkout .cs_checkout_login_register").hide(200);
    $(".woocommerce-checkout .cs_hide_checkout").each(function () {
      $(this).show(200);
    });
  });

  $("body").on("click", ".cs_order_back_to_login", function (e) {
    e.preventDefault();
    $(".woocommerce-checkout .cs_hide_checkout").each(function () {
      $(this).hide(200);
    });
    $(".woocommerce-checkout .cs_checkout_login_register").show(200);
  });

  var order_id = $(".cs_order_track_bar").attr("data-order_id");
  if (typeof order_id !== "undefined") {
    setInterval(function () {
      var data = new FormData();
      data.append("order_id", order_id);
      data.append("action", "cs_refresh_order_status");
      $.ajax({
        type: "post",
        url: cs.ajaxurl,
        data: data,
        dataType: "HTML",
        cache: false,
        processData: false,
        contentType: false,
        success: function (res) {
          $(".cs_order_track_bar").html(res);
        },
      });
    }, 1000 * 5);
  }
  $(document).on(
    "click",
    ".pop-up-modal .cs-select-store.cs-enabled",
    function () {
      $(".pop-up-modal").addClass("cs_loading");
    }
  );
  $("body").on(
    "click",
    ".woocommerce-cart-form .quantity > button",
    function () {
      //console.log($(this).text());
      $(".woocommerce-cart-form .actions button[name|='update_cart']").trigger(
        "click"
      );
      //     	$('.pop-up-modal').addClass('cs_loading');
    }
  );

  // $('body').on('click', '.cs_customer_support_order', function () {
  //   $('body').find('.cs_customer_support_wrapper').toggle();
  // });

  // $("body").on('click', '#cs_support_send', function (e) {
  $(".cs_customer_support_wrapper form").submit(function (e) {
    e.preventDefault();

    var getOrderId = $("#cs_support_order_id").val();
    var getSubject = $("#cs_support_subject").val();
    var getMessage = $("#cs_support_message").val();

    var data = new FormData();
    data.append("order_id", getOrderId);
    data.append("subject", getSubject);
    data.append("message", getMessage);
    data.append("action", "cs_add_support_message");

    $.ajax({
      type: "post",
      url: cs.ajaxurl,
      data: data,
      dataType: "application/json",
      cache: false,
      processData: false,
      contentType: false,
      success: function (res) {
        $(".cs_customer_support_wrapper form .cs_support_alert").show();
        $(".cs_customer_support_wrapper form fieldset").hide();
        //cs_support_alert
        setInterval(function () {
          $(".cs_customer_support_wrapper form .cs_support_alert").hide();
          $(".cs_customer_support_wrapper form fieldset").show();
          $(".cs_customer_support_wrapper").hide();
        }, 1000 * 5);
      },
    });
  });


});
// source --> https://clickcollect.chickenspot.com/wp-content/themes/fast-food/framework/js/html5shiv.min.js?ver=1 
'use strict';(function(l,f){function m(){var a=e.elements;return"string"==typeof a?a.split(" "):a}function i(a){var b=n[a[o]];b||(b={},h++,a[o]=h,n[h]=b);return b}function p(a,b,c){b||(b=f);if(g){return b.createElement(a)}c||(c=i(b));b=c.cache[a]?c.cache[a].cloneNode():r.test(a)?(c.cache[a]=c.createElem(a)).cloneNode():c.createElem(a);return b.canHaveChildren&&!s.test(a)?c.frag.appendChild(b):b}function t(a,b){if(!b.cache){b.cache={},b.createElem=a.createElement,b.createFrag=a.createDocumentFragment,b.frag=b.createFrag()}a.createElement=function(c){return !e.shivMethods?b.createElem(c):p(c,a,b)};a.createDocumentFragment=Function("h,f","return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&("+m().join().replace(/[\w\-]+/g,function(a){b.createElem(a);b.frag.createElement(a);return'c("'+a+'")'})+");return n}")(e,b.frag)}function q(a){a||(a=f);var b=i(a);if(e.shivCSS&&!j&&!b.hasCSS){var c,d=a;c=d.createElement("p");d=d.getElementsByTagName("head")[0]||d.documentElement;c.innerHTML="x<style>article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}mark{background:#FF0;color:#000}template{display:none}</style>";c=d.insertBefore(c.lastChild,d.firstChild);b.hasCSS=!!c}g||t(a,b);return a}var k=l.html5||{},s=/^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i,r=/^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i,j,o="_html5shiv",h=0,n={},g;(function(){try{var a=f.createElement("a");a.innerHTML="<xyz></xyz>";j="hidden" in a;var b;if(!(b=1==a.childNodes.length)){f.createElement("a");var c=f.createDocumentFragment();b="undefined"==typeof c.cloneNode||"undefined"==typeof c.createDocumentFragment||"undefined"==typeof c.createElement}g=b}catch(d){g=j=!0}})();var e={elements:k.elements||"abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output progress section summary template time video",version:"3.7.0",shivCSS:!1!==k.shivCSS,supportsUnknownElements:g,shivMethods:!1!==k.shivMethods,type:"default",shivDocument:q,createElement:p,createDocumentFragment:function(a,b){a||(a=f);if(g){return a.createDocumentFragment()}for(var b=b||i(a),c=b.frag.cloneNode(),d=0,e=m(),h=e.length;d<h;d++){c.createElement(e[d])}return c}};l.html5=e;q(f)})(this,document);
// source --> https://clickcollect.chickenspot.com/wp-content/themes/fast-food/framework/js/respond.min.js?ver=1 
'use strict';
/*! matchMedia() polyfill - Test a CSS media type/query in JS. Authors & copyright (c) 2012: Scott Jehl, Paul Irish, Nicholas Zakas. Dual MIT/BSD license */
/*! NOTE: If you're already including a window.matchMedia polyfill via Modernizr or otherwise, you don't need this part */
window.matchMedia=window.matchMedia||function(a){"use strict";var c,d=a.documentElement,e=d.firstElementChild||d.firstChild,f=a.createElement("body"),g=a.createElement("div");return g.id="mq-test-1",g.style.cssText="position:absolute;top:-100em",f.style.background="none",f.appendChild(g),function(a){return g.innerHTML='&shy;<style media="'+a+'"> #mq-test-1 { width: 42px; }</style>',d.insertBefore(f,e),c=42===g.offsetWidth,d.removeChild(f),{matches:c,media:a}}}(document);

/*! Respond.js v1.3.0: min/max-width media query polyfill. (c) Scott Jehl. MIT/GPLv2 Lic. j.mp/respondjs  */
(function(a){"use strict";function x(){u(!0)}var b={};if(a.respond=b,b.update=function(){},b.mediaQueriesSupported=a.matchMedia&&a.matchMedia("only all").matches,!b.mediaQueriesSupported){var q,r,t,c=a.document,d=c.documentElement,e=[],f=[],g=[],h={},i=30,j=c.getElementsByTagName("head")[0]||d,k=c.getElementsByTagName("base")[0],l=j.getElementsByTagName("link"),m=[],n=function(){for(var b=0;l.length>b;b++){var c=l[b],d=c.href,e=c.media,f=c.rel&&"stylesheet"===c.rel.toLowerCase();d&&f&&!h[d]&&(c.styleSheet&&c.styleSheet.rawCssText?(p(c.styleSheet.rawCssText,d,e),h[d]=!0):(!/^([a-zA-Z:]*\/\/)/.test(d)&&!k||d.replace(RegExp.$1,"").split("/")[0]===a.location.host)&&m.push({href:d,media:e}))}o()},o=function(){if(m.length){var b=m.shift();v(b.href,function(c){p(c,b.href,b.media),h[b.href]=!0,a.setTimeout(function(){o()},0)})}},p=function(a,b,c){var d=a.match(/@media[^\{]+\{([^\{\}]*\{[^\}\{]*\})+/gi),g=d&&d.length||0;b=b.substring(0,b.lastIndexOf("/"));var h=function(a){return a.replace(/(url\()['"]?([^\/\)'"][^:\)'"]+)['"]?(\))/g,"$1"+b+"$2$3")},i=!g&&c;b.length&&(b+="/"),i&&(g=1);for(var j=0;g>j;j++){var k,l,m,n;i?(k=c,f.push(h(a))):(k=d[j].match(/@media *([^\{]+)\{([\S\s]+?)$/)&&RegExp.$1,f.push(RegExp.$2&&h(RegExp.$2))),m=k.split(","),n=m.length;for(var o=0;n>o;o++)l=m[o],e.push({media:l.split("(")[0].match(/(only\s+)?([a-zA-Z]+)\s?/)&&RegExp.$2||"all",rules:f.length-1,hasquery:l.indexOf("(")>-1,minw:l.match(/\(\s*min\-width\s*:\s*(\s*[0-9\.]+)(px|em)\s*\)/)&&parseFloat(RegExp.$1)+(RegExp.$2||""),maxw:l.match(/\(\s*max\-width\s*:\s*(\s*[0-9\.]+)(px|em)\s*\)/)&&parseFloat(RegExp.$1)+(RegExp.$2||"")})}u()},s=function(){var a,b=c.createElement("div"),e=c.body,f=!1;return b.style.cssText="position:absolute;font-size:1em;width:1em",e||(e=f=c.createElement("body"),e.style.background="none"),e.appendChild(b),d.insertBefore(e,d.firstChild),a=b.offsetWidth,f?d.removeChild(e):e.removeChild(b),a=t=parseFloat(a)},u=function(b){var h="clientWidth",k=d[h],m="CSS1Compat"===c.compatMode&&k||c.body[h]||k,n={},o=l[l.length-1],p=(new Date).getTime();if(b&&q&&i>p-q)return a.clearTimeout(r),r=a.setTimeout(u,i),void 0;q=p;for(var v in e)if(e.hasOwnProperty(v)){var w=e[v],x=w.minw,y=w.maxw,z=null===x,A=null===y,B="em";x&&(x=parseFloat(x)*(x.indexOf(B)>-1?t||s():1)),y&&(y=parseFloat(y)*(y.indexOf(B)>-1?t||s():1)),w.hasquery&&(z&&A||!(z||m>=x)||!(A||y>=m))||(n[w.media]||(n[w.media]=[]),n[w.media].push(f[w.rules]))}for(var C in g)g.hasOwnProperty(C)&&g[C]&&g[C].parentNode===j&&j.removeChild(g[C]);for(var D in n)if(n.hasOwnProperty(D)){var E=c.createElement("style"),F=n[D].join("\n");E.type="text/css",E.media=D,j.insertBefore(E,o.nextSibling),E.styleSheet?E.styleSheet.cssText=F:E.appendChild(c.createTextNode(F)),g.push(E)}},v=function(a,b){var c=w();c&&(c.open("GET",a,!0),c.onreadystatechange=function(){4!==c.readyState||200!==c.status&&304!==c.status||b(c.responseText)},4!==c.readyState&&c.send(null))},w=function(){var b=!1;try{b=new a.XMLHttpRequest}catch(c){b=new a.ActiveXObject("Microsoft.XMLHTTP")}return function(){return b}}();n(),b.update=n,a.addEventListener?a.addEventListener("resize",x,!1):a.attachEvent&&a.attachEvent("onresize",x)}})(this);