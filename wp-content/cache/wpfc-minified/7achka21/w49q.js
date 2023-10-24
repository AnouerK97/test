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