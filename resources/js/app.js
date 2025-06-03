import './bootstrap';
import GLightbox from 'glightbox';

/**
 * Initializes or re-initializes GLightbox.
 * It targets anchor tags with the class 'glightbox'.
 */
function initializeLightbox() {
    GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: false,
        openEffect: 'zoom',
        closeEffect: 'fade',
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initializeLightbox();
});

document.addEventListener('livewire:navigated', () => {
    initializeLightbox();
});

window.addEventListener('lightbox:refresh', () => {
    initializeLightbox();
});
