

function betting(clicked_id) {
    var game;
    $.post("/addbet", {data: clicked_id})
        .done(function (data) {
            game = data;
            console.log(data);
            showbettingwindow(game);
        });
};

function showbettingwindow(game) {

    $("#dialog-modal").dialog({
            width: 300,
            height: 200,
            open: function(event, ui)
            {
                dialogClass:'dialog_style1';
            }
        });
}


