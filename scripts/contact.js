document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    const inputs = contactForm.querySelectorAll('input, textarea');

    const checkFormValidity = () => {
        // isEnabled will be true only if all required fields pass their HTML5 checks
        const isEnabled = contactForm.checkValidity();
        submitBtn.disabled = !isEnabled;
    };

    // Listen for typing events on all inputs
    inputs.forEach(input => {
        input.addEventListener('input', checkFormValidity);
    });

    // Initial check
    checkFormValidity();
});