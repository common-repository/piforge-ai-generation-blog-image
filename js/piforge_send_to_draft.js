jQuery(document).ready(function ($) {
  $(".saveDraftBtn").on("click", function (e) {
    e.preventDefault();

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
    // Récupérer le contenu de l'éditeur WordPress
    let iframe = document.getElementById("auto-blog-editor_ifr");
    // Vérifier si l'iframe existe
    if (iframe) {
      // Accéder au contenu de l'iframe
      let iframeContent = iframe.contentWindow.document;

      // Vérifier si le contenu de l'iframe existe
      if (iframeContent) {
        // Nouveau contenu récupéré
        let editorContent = iframeContent.getElementById("tinymce").innerHTML;
        if (!editorContent) {
          error_notice("An error was ocured please refresh your navigator.");
          console.error("An error was ocured please refresh your navigator.");
          return;
        }
        let tempDiv = document.createElement("div");
        tempDiv.innerHTML = editorContent;
        // Récupérer le contenu de la balise <h1>

        let h1 = tempDiv.querySelector("h1");
        if (!h1) {
          error_notice("No title in your content (H1 html) ");
          return;
        }
        let h1Text = h1.innerText;
        tempDiv.remove();
        let nonce = document
          .getElementById("save_draft_button")
          .getAttribute("data-nonce");
        // Envoyer le contenu au serveur via une requête AJAX
        $.ajax({
          url: ajaxurl, // URL de l'API WordPress AJAX
          type: "POST",
          data: {
            action: "piforge_save_draft_request", // Action AJAX
            content: editorContent, // Contenu de l'éditeur
            title: h1Text ? h1Text : "New Draft",
            nonce: nonce,
          },
          success: function () {
            // Afficher un message de confirmation ou effectuer d'autres actions
            success_notice("Draft saved successfully.");
            console.log("Draft saved successfully.");
          },
          error: function () {
            // Afficher un message d'erreur ou effectuer d'autres actions en cas d'erreur
            error_notice("Error saving draft.");
            console.error("Error saving draft.");
          },
        });
      }
    }
  });
});
