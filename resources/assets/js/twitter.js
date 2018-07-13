function twitterWidget(tweets, target) {
    var statusHTML = [];

    for (var i = 0; i < tweets.length; i++) {
        var template = '<li>\
            <p class="tweet-text">%text%</p>\
            <p class="tweet-meta">\
                <a href="https://twitter.com/%screen_name%/statuses/%tweet_id%">%relative_time%</a>\
            </p>\
        </li>';

        // Replace template tags
        var status = '';
        status = template.replace('%text%', tweets[i].text);
        status = status.replace('%screen_name%', tweets[i].username);
        status = status.replace('%tweet_id%', tweets[i].id);
        status = status.replace('%relative_time%', tweets[i].relative_time);

        statusHTML.push(status);
    }

    $(target).html(statusHTML.join(''));
}
