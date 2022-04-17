function likeVideo(button, videoId) {
    $.post("ajax/likeVideo.php", {videoId: videoId})
        .done(function (data) {

            let likeButton = $(button);
            let dislikeButton = $(button).siblings(".dislikeButton");

            likeButton.addClass("active");
            dislikeButton.removeClass("active");

            let result = JSON.parse(data);
            updateLikesValue(likeButton.find(".text"), result.likes);
            updateLikesValue(dislikeButton.find(".text"), result.dislikes);

            if(result.likes < 0) {
                likeButton.removeClass("active");
                likeButton.find("img:first").attr("src", "images/icons/like.png");
            } else {
                likeButton.find("img:first").attr("src", "images/icons/like-active.png");
            }

            dislikeButton.find("img:first").attr("src", "images/icons/dislike.png");
        });
}

function dislikeVideo(button, videoId) {
    $.post("ajax/dislikeVideo.php", {videoId: videoId})
        .done(function (data) {

            let dislikeButton = $(button);
            let likeButton = $(button).siblings(".likeButton");

            dislikeButton.addClass("active");
            likeButton.removeClass("active");

            let result = JSON.parse(data);
            updateLikesValue(likeButton.find(".text"), result.likes);
            updateLikesValue(dislikeButton.find(".text"), result.dislikes);

            if(result.dislikes < 0) {
                dislikeButton.removeClass("active");
                dislikeButton.find("img:first").attr("src", "images/icons/dislike.png");
            } else {
                dislikeButton.find("img:first").attr("src", "images/icons/dislike-active.png");
            }

            likeButton.find("img:first").attr("src", "images/icons/like.png");
        });
}

function updateLikesValue(element, num) {
    let likesCountValue = element.text() || 0;
    element.text(parseInt(likesCountValue) + parseInt(num));
}