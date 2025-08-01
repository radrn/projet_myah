document.querySelectorAll('.edit-btn').forEach(button => {

    button.addEventListener('click', function () {
        const userId = this.dataset.userId;
        let followers = document.querySelector('[data-span-followers-id="' + userId + '"]');
        const url = `/profile_ajax/handleFollow/` + userId;
        console.log(url);
        fetch(url)
            .then((response) => {
                console.log(response);
                return response.json();
            }).then((data) => {
            console.log(data)
            if (data.following) {
                button.textContent = "Abonné";
                followers.textContent = data.followersCount + 'abonnés';
            } else {
                button.textContent = "Suivre";
                followers.textContent = data.followersCount + 'abonnés';
            }
        })

    })
})
