$(document).ready(function(){
    $(".dismiss").click(function(){
        var data = {
            action: "usernotifications/removeNotification",
            id: $(this).data('id')
        };
        var button = $(this);
        $.ajax({
            type: "post",
            url: '',
            data: data,
            success: function(data){
                if(data.success ===  true){
                    button.parent().hide();
                }else{
                    // something went wrong display error message
                    alert(data.message);
                }
            },
            error: function (XMLHttpRequest, textStatus) {
                console.log("Status: " + textStatus);
            }
        });
    });
});
