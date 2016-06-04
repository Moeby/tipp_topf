/**
 * Created by elena on 04.06.16.
 */

var QUERY_PHASE_1 = 'http://api.football-data.org/v1/soccerseasons/424/fixtures?matchday=1';
var QUERY_PHASE_2 = 'http://api.football-data.org/v1/soccerseasons/424/fixtures?matchday=2';
var QUERY_PHASE_3 = 'http://api.football-data.org/v1/soccerseasons/424/fixtures?matchday=3';
var QUERY_PHASE_4 = 'http://api.football-data.org/v1/soccerseasons/424/fixtures?matchday=4';
var QUERY_PHASE_5 = 'http://api.football-data.org/v1/soccerseasons/424/fixtures?matchday=5';
var QUERY_PHASE_6 = 'http://api.football-data.org/v1/soccerseasons/424/fixtures?matchday=6';
var QUERY_PHASE_7 = 'http://api.football-data.org/v1/soccerseasons/424/fixtures?matchday=7';

/** 
 * RESTful football data
 * http://api.football-data.org/v1/soccerseasons/424
 * caption: "European Championships France 2016",
 * numberOfMatchdays: 6,
 * numberOfTeams: 24,
 * numberOfGames: 36,
 */

$(document).ready(function() {

    $.ajax({
        headers: { 'X-Auth-Token': '0870114276c34fe8b08a5d426955bf01' },
        url: 'http://api.football-data.org/v1/soccerseasons/424/fixtures',
        dataType: 'json',
        type: 'GET',
        success: function (data) {
            console.log(data);
        },
        error: function (xhr, status) {
            alert("error");
        }
    });

} );
