jQuery(document).ready(function ($) {
  $("#piforge_request_image_form").submit(function (event) {
    event.preventDefault();
    function error_notice(message) {
      let wrap = document.getElementById("wrap");
      let errorDiv = document.createElement("div");
      errorDiv.setAttribute("class", "notice notice-error is-dismissible");

      let par = document.createElement("p");
      par.innerText = message;
      errorDiv.appendChild(par);
      wrap.insertBefore(errorDiv, wrap.firstChild);
    }
    function success_notice(message) {
      let wrap = document.getElementById("wrap");
      let errorDiv = document.createElement("div");
      errorDiv.setAttribute("class", "notice notice-success is-dismissible");

      let par = document.createElement("p");
      par.innerText = message;
      errorDiv.appendChild(par);
      wrap.insertBefore(errorDiv, wrap.firstChild);
    }
    function create_images_elemnt(img_urls) {
      let image_container = document.getElementById("image_container");
      img_urls.forEach((element) => {
        let img = document.createElement("img");
        img.src = element;
        img.style.maxWidth = "100%";
        img.style.alignSelf = "flex-start";

        image_container.insertBefore(img, image_container.firstChild);
      });
    }

    let data = {
      action: "piforge_image_request", // Action WordPress AJAX
      prompt: $("#prompt").val(), // Retrieve form data
      style_gen: $("#style_gen").val(), // Retrieve form data
      count: $("#count").val(), // Retrieve form data
      ratio: $("#ratio").val(), // Retrieve form data
      product: $("#product").val(), // Retrieve form data
      image: $("#image").val(),
    };
    console.log("Requested => ", data);

    let button_elem = document.getElementById("form_button_1");
    button_elem.disabled = true;
    button_elem.value = "In progress";
    let spinner = document.getElementById("generate__spinner");
    spinner.setAttribute("class", "spinner is-active");

    let old_notices = document.getElementsByClassName("notice");
    while (old_notices.length > 0) {
      old_notices[0].remove();
    }
    $.post(piforge_image_script.ajaxurl, data, function (response) {
      console.log("success", response);
      if (!response) {
        return;
      }

      let button_elem = document.getElementById("form_button_1");
      button_elem.disabled = false;
      button_elem.value = "Generate";
      let spinner = document.getElementById("generate__spinner");
      spinner.setAttribute("class", "spinner");

      success_notice(
        "Successful created and saved " +
          response.data.img_urls.length +
          " images"
      );
      create_images_elemnt(response.data.img_urls);
    }).fail(function (error) {
      console.log("Eroorr", error);
      if (!error) {
        return;
      }
      let spinner = document.getElementById("generate__spinner");
      spinner.setAttribute("class", "spinner");
      let button_elem = document.getElementById("form_button_1");
      button_elem.disabled = false;
      button_elem.value = "Generate";
      error_notice(error.responseJSON?.data.message ?? error.statusText);
    });

    // Prevent the form from submitting normally
    return false;
  });
});
