document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll(".card");

    const observer = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("show");
                    observer.unobserve(entry.target); // Stops re-triggering
                }
            });
        },
        {
            root: null,
            threshold: 0.1, // Triggers sooner
            rootMargin: "0px 0px -50px 0px" // Ensures visibility trigger
        }
    );

    cards.forEach(card => observer.observe(card));
});
