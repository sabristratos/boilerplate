import './bootstrap';
import GLightbox from 'glightbox';
import { gsap } from "gsap";

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

document.addEventListener('DOMContentLoaded', () => {

    gsap.fromTo(".gsap-button",
        {
            opacity: 0,
            y: 10,
        },
        {
            opacity: 1,
            y: 0,
            duration: 0.3,
            ease: "sine.out",
            stagger: 0.2,
        }
    );



    const buttons = document.querySelectorAll('.gsap-button');

    buttons.forEach(button => {

        const hoverTimeline = gsap.timeline({ paused: true });

        hoverTimeline.to(button, {
            y: -3,
            scale: 1.04,
            duration: 0.1,
            ease: "power1.inOut"
        });

        button.addEventListener('mouseenter', () => {
            hoverTimeline.play();
        });

        button.addEventListener('mouseleave', () => {
            hoverTimeline.reverse();
        });
    });

});
