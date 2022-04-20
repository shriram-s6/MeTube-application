function postComment(button, postedBy, videoId, responseTo, classContainer) {

    let textArea = $(button).siblings("textarea");
    let commentText = textArea.val();
    textArea.val("");

    if(commentText) {
        $.post("ajax/postComment.php", {commentText: commentText, postedBy: postedBy, videoId: videoId, responseTo: responseTo})
            .done(function (comment) {
                $("." + classContainer).prepend(comment);
            })
    } else {
        alert("Your comment must not be empty");
    }
}

function toggleReply(button) {
    let parent = $(button).closest(".itemContainer");
    let commentForm = parent.find(".commentForm").first();

    commentForm.toggleClass("hidden");
}

function likeComment(commentId, button, videoId) {
    $.post("ajax/likeComment.php", {commentId: commentId, videoId: videoId})
        .done(function (numToChange) {

            let likeButton = $(button);
            let dislikeButton = $(button).siblings(".dislikeButton");

            likeButton.addClass("active");
            dislikeButton.removeClass("active");

            let likesCount = $(button).siblings(".likesCount");
            updateLikesValue(likesCount, numToChange);

            if(numToChange < 0) {
                likeButton.removeClass("active");
                likeButton.find("img:first").attr("src", "images/icons/like.png");
            } else {
                likeButton.find("img:first").attr("src", "images/icons/like-active.png");
            }

            dislikeButton.find("img:first").attr("src", "images/icons/dislike.png");
        });
}

function dislikeComment(commentId, button, videoId) {
    $.post("ajax/dislikingComments.php", {commentId: commentId, videoId: videoId})
        .done(function (numToChange) {

            let dislikeButton = $(button);
            let likeButton = $(button).siblings(".likeButton");

            dislikeButton.addClass("active");
            likeButton.removeClass("active");

            let likesCount = $(button).siblings(".likesCount");
            updateLikesValue(likesCount, numToChange);

            if(numToChange > 0) {
                dislikeButton.removeClass("active");
                dislikeButton.find("img:first").attr("src", "images/icons/dislike.png");
            } else {
                dislikeButton.find("img:first").attr("src", "images/icons/dislike-active.png");
            }

            dislikeButton.find("img:first").attr("src", "images/icons/like.png");
        });
}

function updateLikesValue(element, num) {
    let likesCountValue = element.text() || 0;
    element.text(parseInt(likesCountValue) + parseInt(num));
}