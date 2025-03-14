const backToTop = document.getElementById('backToTop');

backToTop.style.display = 'none';
window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
        backToTop.style.display = 'flex';
    } else {
        backToTop.style.display = 'none';
    }
});