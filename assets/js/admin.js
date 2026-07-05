document.addEventListener("DOMContentLoaded", function () {

    const category = document.querySelector('input[name="category_name"]');
    const slug = document.querySelector('input[name="slug"]');

    if (category && slug) {

        category.addEventListener("keyup", function () {

            slug.value = category.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9]+/g, "-")
                .replace(/^-+|-+$/g, "");

        });

    }

});