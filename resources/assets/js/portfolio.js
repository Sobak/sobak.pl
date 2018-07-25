$(function() {
    $('.portfolio .filters li a').click(function () {
        $(this).css('outline','none');
        $('.portfolio .filters .current').removeClass('current');
        $(this).parent().addClass('current');

        var filter = $(this).data('type');
        if (filter === 'all') {
            $('.portfolio .projects li.hidden').fadeIn('normal').removeClass('hidden');
        } else {
            $('.portfolio .projects li').each(function () {
                project = $(this);
                if(project.hasClass(filter) === false) {
                    project.fadeOut('slow').addClass('hidden');
                } else {
                    project.fadeIn('slow').removeClass('hidden');
                }
            });
        }
        return false;
    });
});
