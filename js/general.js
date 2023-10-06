const errors = document.querySelector('.errors');
const news = document.querySelector('.news');

if (errors) {
    const btn = errors.querySelector('.close-new');
    btn.addEventListener('click', () => {
        errors.classList.add('remove');
    });
}
if (news) {
    const btn = news.querySelector('.close-new');
    btn.addEventListener('click', () => {
        news.classList.add('remove');
    });
}

const scrollButton = document.querySelector('.scroll-down');
const scrollDestination = document.querySelector('.scroll-dest');
if (scrollButton && scrollDestination)
    scrollButton.addEventListener('click', () => {
        scrollDestination.scrollIntoView({ behavior: 'smooth' });
    });