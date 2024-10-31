jQuery(document).ready(function ($) {
  $("#piforge_request_blog_form").submit(function (event) {
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

    let data = {
      action: "piforge_blog_request", // Action WordPress AJAX
      target: $("#target").val(),
      subject: $("#subject").val(),
      word_number: $("#word_number").val(),
      imperative_word: $("#imperative_word").val(),
      structure: $("#structure").val(),
      language: $("#language").val(),
      blog: $("#blog").val(),
    };
    console.log("Requested => ", JSON.stringify(data));

    let button_elem = document.getElementById("form_button_2");
    button_elem.disabled = true;
    button_elem.value = "In progress";
    let spinner = document.getElementById("generate__spinner");
    spinner.setAttribute("class", "spinner is-active");

    let old_notices = document.getElementsByClassName("notice");
    while (old_notices.length > 0) {
      old_notices[0].remove();
    }
    $.post(piforge_blog_script.ajaxurl, data, function (response) {
      console.log(response);
      if (!response) {
        return;
      }
      let spinner = document.getElementById("generate__spinner");
      spinner.setAttribute("class", "spinner");
      let button_elem = document.getElementById("form_button_2");
      button_elem.disabled = false;
      button_elem.value = "Generate";
      let iframe = document.getElementById("auto-blog-editor_ifr");
      if (iframe) {
        let iframeContent = iframe.contentWindow.document;
        if (iframeContent) {
          let newContent = response.data.data.choices[0].message.content;

          // Modifier le contenu de l'éditeur à l'intérieur de l'iframe
          iframeContent.getElementById("tinymce").innerHTML = newContent;
          success_notice("Successful created your blog content");
        } else {
          error_notice("Editor content not found. Please try again later");
        }
      } else {
        error_notice("Editor not found. Please try again later");
      }
    }).fail(function (error) {
      console.log(error);
      if (!error) {
        return;
      }
      let spinner = document.getElementById("generate__spinner");
      spinner.setAttribute("class", "spinner");
      let button_elem = document.getElementById("form_button_2");
      button_elem.disabled = false;
      button_elem.value = "Generate";
      error_notice(error.responseJSON?.message ?? error.statusText);
    });
    return false;
  });
});
