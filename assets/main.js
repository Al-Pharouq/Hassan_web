AOS.init({
    duration: 1000, // Animation duration in milliseconds
    offset: 120, // Offset (in pixels) from the original trigger point
    delay: 200, // Delay in milliseconds
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