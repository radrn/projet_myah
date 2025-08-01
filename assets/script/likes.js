document.querySelectorAll('.likebtn').forEach(button => {

    button.addEventListener('click', function () {
        const postId = this.dataset.postId;
        let span = document.querySelector('[data-span-post-id="' + postId + '"]');
        const url = `/post_ajax/handleLike/` + postId;
        
        fetch(url)
            .then((response) => {
                return response.json();
            }).then((data) => {
            if (data.liked) {
                button.setAttribute('fill', '#82669e');
                span.textContent = data.count;
            } else {
                button.setAttribute('fill', '#A2AA7D');
                span.textContent = data.count;
            }

        })
    })
})

