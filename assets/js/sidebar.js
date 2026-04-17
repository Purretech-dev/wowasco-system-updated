document.addEventListener("DOMContentLoaded", function () {

    const toggles = document.querySelectorAll(".toggle-btn");

    toggles.forEach(toggle => {
        toggle.addEventListener("click", function () {

            const parent = this.parentElement;

            // Close other open menus
            document.querySelectorAll(".menu-item").forEach(item => {
                if (item !== parent) {
                    item.classList.remove("active");
                }
            });

            // Toggle current menu
            parent.classList.toggle("active");

        });
    });

});