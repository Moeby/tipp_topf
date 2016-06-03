
var counter = 0;
//  add "invite people" rows in form
$(document).on("click", "#link", function(){
    counter++
   $( "#link" ).before( "<label>Invite Person (Email):</label><input type='email' name='person"+counter+"' maxlength='90'><br>" );
});