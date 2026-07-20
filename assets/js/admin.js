document.addEventListener("DOMContentLoaded", function () {

    // Auto slug generator
    const title = document.getElementById("title");
    const slug = document.getElementById("slug");

    if (title && slug) {
        title.addEventListener("keyup", function () {

            slug.value = title.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');

        });
    }

});