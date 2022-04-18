function subscribe(subscribedTo, subscribedFrom, button) {
    if(subscribedTo === subscribedFrom) {
        alert("You can't subscribe to your own channel");
        return;
    }

    $.post("ajax/subscribe.php", {subscribedTo: subscribedTo, subscribedFrom: subscribedFrom})
        .done(function (count){
            if(count !== null) {
                $(button).toggleClass("subscribe unsubscribe");

                let buttonText = $(button).hasClass("subscribe") ? "Subscribe" : "Subscribed";
                $(button).text(buttonText + " " + count);
            }
        }
        );
}