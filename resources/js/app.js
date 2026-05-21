import "./bootstrap";

document.addEventListener("DOMContentLoaded", () => {
    // Logic Scroll Reveal Animation
    const observerOptions = {
        root: null,
        rootMargin: "0px",
        threshold: 0.15,
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("visible");
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    const revealElements = document.querySelectorAll(".reveal");
    revealElements.forEach((el) => observer.observe(el));

    // Logic ẩn/hiện Nav Header khi scroll
    const header = document.getElementById("navbar");
    window.addEventListener("scroll", () => {
        if (window.scrollY > 50) {
            header.classList.add("header-glass", "py-2");
            header.classList.remove("py-3", "bg-espresso");
        } else {
            header.classList.remove("header-glass", "py-2");
            header.classList.add("py-3", "bg-espresso");
        }
    });
});
