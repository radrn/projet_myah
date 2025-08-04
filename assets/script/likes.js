// On sélectionne tous les boutons avec la classe "likebtn" (icônes cœur)
document.querySelectorAll('.likebtn').forEach(button => {

    // Pour chaque bouton, on ajoute un écouteur d'événement "click"
    button.addEventListener('click', function () {
        // On récupère l’ID du post à partir de l’attribut data-post-id
        const postId = this.dataset.postId;

        // On sélectionne le <span> qui affiche le nombre de likes, en ciblant son data-span-post-id
        let span = document.querySelector('[data-span-post-id="' + postId + '"]');

        // On prépare l’URL pour appeler la route Symfony en AJAX
        const url = `/post_ajax/handleLike/` + postId;

        // On effectue la requête fetch (méthode GET par défaut) vers l’URL
        fetch(url)
            .then((response) => {
                return response.json(); // On transforme la réponse en JSON
            }).then((data) => {
            // Si le post a été liké (data.liked = true)
            if (data.liked) {
                // On change la couleur de l’icône pour indiquer que le post est liké
                button.setAttribute('fill', '#82669e');
                // On met à jour le nombre de likes affiché dans le span
                span.textContent = data.count;
            } else {
                // Si le post a été dé-liké, on remet la couleur d’origine
                button.setAttribute('fill', '#A2AA7D');
                // On met à jour le nombre de likes affiché dans le span
                span.textContent = data.count;
            }
        });
    });
});
