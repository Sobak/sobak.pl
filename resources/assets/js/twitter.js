function twitterWidget(tweets, target, options) {
    var statusHTML = [];
    var count = tweets.length;

    // Get count and offset options
    var offset = 1;
    if (options.offset) offset = options.offset
    offset -= 1;
    if (options.count) count = options.count + offset;
    if (count > tweets.length) count = tweets.length;

    for (var i = offset; i < count; i++) {
        var username = tweets[i].user.screen_name;
        var status = tweets[i].text;

        // Linkify links
        status = status.replace(/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;'">\:\s\<\>\)\]\!])/g, function (url) {
            return '<a href="' + url + '">' + url + '</a>';
        });

        // Linkify @
        status = status.replace(/\B@([_a-z0-9]+)/ig, function (reply) {
            return reply.charAt(0) + '<a href="http://twitter.com/#!/' + reply.substring(1) + '">' + reply.substring(1) + '</a>';
        });

        // Linkify hashtags
        status = status.replace(/(^|[^&\w'"]+)\#([a-zA-Z0-9_^"^<]+)/g, function (m, m1, m2) {
            return m.substr(-1) === '"' || m.substr(-1) == '<' ? m : m1 + '<strong>#<a href="http://twitter.com/#!/search/%23' + m2 + '">' + m2 + '</a></strong>';
        });

        // Replace line breaks with <br>
        status = status.replace(/\n/g, '<br>');

        // Get template
        var template = '<li>\
            <p class="twiget-tweet">%text%</p>\
            <p class="twiget-meta">\
                <span class="timestamp"><a href="http://twitter.com/%screen_name%/statuses/%tweet_id%">%relative_time%</a></span>\
            </p>\
        </li>';

        // Replace template tags
        status = template.replace('%text%', status);
        status = status.replace('%screen_name%', username);
        status = status.replace('%tweet_id%', tweets[i].id_str);
        status = status.replace('%relative_time%', twitterRelativeTime(tweets[i].created_at));

        statusHTML.push(status);
    }
    document.getElementById(target).innerHTML = statusHTML.join('');
}

function twitterRelativeTime(time_value) {
    var values = time_value.split(" ");
    time_value = values[1] + " " + values[2] + ", " + values[5] + " " + values[3];
    var parsed_date = Date.parse(time_value);
    var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
    var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
    delta = delta + (relative_to.getTimezoneOffset() * 60);

    if (delta < 60) {
        return 'mniej niż minutę temu';
    } else if (delta < 120) {
        return 'około minuty temu';
    } else if (delta < (60 * 60)) {
        var relTimeText = '%reltime% minut temu';
        return relTimeText.replace('%reltime%', (parseInt(delta / 60)).toString());
    } else if (delta < (120 * 60)) {
        return 'około godzinę temu';
    } else if (delta < (24 * 60 * 60)) {
        var relTimeText = 'około %reltime% godzin temu';
        return relTimeText.replace('%reltime%', (parseInt(delta / 3600)).toString());
        ;
    } else if (delta < (48 * 60 * 60)) {
        return 'wczoraj';
    } else {
        var relTimeText = '%reltime% dni temu';
        return relTimeText.replace('%reltime%', (parseInt(delta / 86400)).toString());
    }
}
