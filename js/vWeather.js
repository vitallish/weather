/**
 * Created with JetBrains PhpStorm.
 * User: Vitaly
 * Date: 9/25/13
 * Time: 9:31 PM
 * To change this template use File | Settings | File Templates.
 */

function updateWthrStats() {
    var oSelData = document.getElementById('select_data');
    var oSelType = document.getElementById('select_type');
    if (oSelType.value == "hourly") {
        var oSelLen = document.getElementById('select_hour');
    } else {
        var oSelLen = document.getElementById('select_day');
    }
    var dataString = "selData=" + oSelData.value + "&selType=" + oSelType.value + "&selLen=" + oSelLen.value;


    if (oSelData.value == 'Precipitation') {
        var oSelPop = document.getElementById('select_pop');
        dataString += "&selPop=" + oSelPop.value;
    }


    $.ajax({
        type: "POST",
        url: siteurl + 'weather/fetchWthrStats',
        data: dataString,
        success: function (result) {
            document.getElementById('wthr_output').innerHTML = result;
        },
        error: function () {
            alert('Something has gone horribly wrong. Sorry. I don\'t even know what it could have been.');
        }
    });

}

//Main Code to run on document load.
if ((oSelData = document.getElementById('select_data')) != null) {
    oSelData.onchange = updateWthrStats;
    document.getElementById('select_type').onchange = updateWthrStats;
    document.getElementById('select_hour').onchange = updateWthrStats;
    document.getElementById('select_day').onchange = updateWthrStats;
    document.getElementById('select_pop').onchange = updateWthrStats;
}





