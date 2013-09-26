/**
 * Created with JetBrains PhpStorm.
 * User: Vitaly
 * Date: 9/25/13
 * Time: 9:31 PM
 * To change this template use File | Settings | File Templates.
 */

document.getElementById('startDate').onchange=function(){
    var date = this.value;
    var dataString = "date="+date;

    $.ajax({
        type:"POST",
        url: 'http://localhost/weather/index.php/testpage/ajaxTest',
        data:dataString,
        success:function(result){
            document.getElementById('ajaxTest').innerHTML = result;
        },
        error: function(){
            alert('Site List was not loaded properly, please let Vitaly know!');
        }
    });

};




