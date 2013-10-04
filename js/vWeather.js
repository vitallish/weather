/**
 * Created with JetBrains PhpStorm.
 * User: Vitaly
 * Date: 9/25/13
 * Time: 9:31 PM
 * To change this template use File | Settings | File Templates.
 */

function updateWthrStats(){
    var oSelData = document.getElementById('select_data');
    var oSelType = document.getElementById('select_type');
    if(oSelData.value=="hourly"){
        var oSelLen = document.getElementById('select_hour');
    }else{
        var oSelLen = document.getElementById('select_day');
    }

    var dataString ="selData="+oSelData.value+"&selType="+oSelType.value+"&selLen="+oSelLen.value;


    $.ajax({
        type:"POST",
        url: siteurl+'weather/fetchWthrStats',
        data:dataString,
        success:function(result){
            document.getElementById('wthr_output').innerHTML = result;
        },
        error: function(){
            alert('Something has gone horribly wrong. Sorry.');
        }
    });

}

//Main Code to run on document load.
if((oSelData = document.getElementById('select_data'))!=null){
    oSelData.onchange=updateWthrStats;
}





