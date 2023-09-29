const errors = document.querySelector('.errorr');
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