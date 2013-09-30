/**
 * Created with JetBrains PhpStorm.
 * User: Vitaly
 * Date: 9/25/13
 * Time: 9:31 PM
 * To change this template use File | Settings | File Templates.
 */

function updateWthrStats(){
    oSelData = document.getElementById('select_data');
    oSelType = document.getElementById('select_type');
    oSelHour = document.getElementById('select_hour');
    oSelDay = document.getElementById('select_day');




}
if((oSelData = document.getElementById('select_data'))!=null){
    oSelData.onchange=updateWthrStats;
}
if((oStartDate = document.getElementById('startDate'))!=null){
    oStartDate.onchange=function(){
        var date = this.value;
        var dataString = "date="+date;

        $.ajax({
            type:"POST",
            url: siteurl+'/testpage/ajaxTest',
            data:dataString,
            success:function(result){
                document.getElementById('ajaxTest').innerHTML = result;
            },
            error: function(){
                alert('Something has gone horribly wrong. Sorry.');
            }
        });

    };
}




