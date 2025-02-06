// Fonction pour faire défiler la page vers la section #figures-section
function scrollToFiguresSection() {
    const section = document.getElementById('figures-section');
    window.scrollTo({
        top: section.offsetTop,
        behavior: 'smooth'
    });
}
