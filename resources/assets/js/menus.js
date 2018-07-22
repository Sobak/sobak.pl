$(function() {
    var menuToggle = $('#menu-toggle');
    var socialLinksToggle = $('#social-links-toggle');
    var searchToggle = $('#search-toggle');

    var menuNav = $('#menu-toggle-nav');
    var socialLinksNav = $('#social-links-toggle-nav');
    var searchNav = $('#search-toggle-nav');

    menuToggle.click(function () {
        menuNav.slideToggle();
        $(this).toggleClass('active');

        searchNav.hide();
        socialLinksNav.hide();

        searchToggle.removeClass('active');
        socialLinksToggle.removeClass('active');
    });

    socialLinksToggle.click(function () {
        socialLinksNav.slideToggle();
        $(this).toggleClass('active');

        menuNav.hide();
        searchNav.hide();

        searchToggle.removeClass('active');
        menuToggle.removeClass('active');
    });

    searchToggle.click(function () {
        searchNav.slideToggle();
        $(this).toggleClass('active');

        // Toggle search input's focus
        var searchInput = $('.search-form input[name="q"]')[0];
        $(this).hasClass('active') ? searchInput.focus() : searchInput.blur();

        socialLinksNav.hide();
        menuNav.hide();

        menuToggle.removeClass('active');
        socialLinksToggle.removeClass('active');
    });
});
