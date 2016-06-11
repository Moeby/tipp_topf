

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
            width: 600,
            height: 400,
            open: function(event, ui)
            {
                dialogClass:'dialog_style1';
                var textarea = $('<textarea style="height: 276px;">');
                $(this).html(textarea);
                $(textarea).redactor({
                    focus: true,
                    maxHeight: 300,
                    callback: {
                        init: function()
                        {
                            this.code.set('<p>Lorem ipsum dolor...</p>');
                        }
                    }
                });
 
            }
        });
}


