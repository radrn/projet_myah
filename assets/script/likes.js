//récuperer tous les data-post-id (ALL!!!)
//boucle sur chacun
//event au click sur chaque
// QAUND CLICK -> recuperer la valeur de data-post-id qur tu va stocker dans une variable postID
// UNE FOIS QUE CA CA MARCHE
//toujours le click
//fetch sur ta route du controller /toto/ + postId

document.querySelectorAll('.likebtn').forEach(button => {
    button.addEventListener('click', function () {
        const postId = this.dataset.postId;
        const url = `/post_ajax/handleLike/` + postId;
        fetch(url)
            .then((response) => {
                return response.json();

            })
            .then((data) => {

                
            })
    })
})

