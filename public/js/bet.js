

function betting(clicked_id, group_id) {;
    var game;
    $.post("/showbet", {data: clicked_id})
        .done(function (data) {
            game = data;
            showbettingwindow(game, group_id);
        });
};

function showbettingwindow(game, group_id) {

    if (game) {
        $("#dialog-modal").dialog({
            width: 250,
            height: 200,
            open: function (event, ui)
            {
                dialogClass:'dialog_style1';
            }
        });

        $('#dialog_form').submit(function (e) {
            // Get all the forms elements and their values in one step
            var values = $(this).serialize();
            var bet = [values, game];
            $.post("/addbet", {data: bet, group_id: group_id})
                .done(function (data) {
                    alert(data);
                    $('#dialog-modal').dialog('destroy');
                    $('#dialog_form').dialog('close').dialog('destroy');
                });
        });
    } else {
        alert('Game is already over / has already started');
    }


}


