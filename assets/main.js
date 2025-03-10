
document.addEventListener("DOMContentLoaded", function() {
    AOS.init({
        duration: 1000,
        delay: 200,
        once: true
    });
});




window.addEventListener('load', function () {
    const loadingPage = document.querySelector('.loading-page');

    // Wait for 2 seconds before hiding the loading page
    setTimeout(function () {
        loadingPage.classList.add('hidden');

        // Remove the loading page from the DOM after the transition ends
        loadingPage.addEventListener('transitionend', function () {
            loadingPage.remove();
        });
    }, 0); // 2000 milliseconds = 2 seconds
});

document.addEventListener('aos:in', function(event) {
    // Remove data-aos attribute to prevent further animations
    event.detail.el.removeAttribute('data-aos');
});
