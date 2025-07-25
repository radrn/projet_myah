const postsBtn = document.getElementById('postsBtn');
const favorisBtn = document.getElementById('favorisBtn');

const postsContent = document.getElementById('postsContent');
const favorisContent = document.getElementById('favorisContent');

postsBtn.addEventListener('click', () => {
    postsBtn.classList.add('active');
    favorisBtn.classList.remove('active');

    postsContent.style.display = 'flex';
    favorisContent.style.display = 'none';
});

favorisBtn.addEventListener('click', () => {
    favorisBtn.classList.add('active');
    postsBtn.classList.remove('active');

    favorisContent.style.display = 'flex';
    postsContent.style.display = 'none';
});
