/**
 * Created with JetBrains PhpStorm.
 * User: Vitaly
 * Date: 9/25/13
 * Time: 9:31 PM
 * To change this template use File | Settings | File Templates.
 */


function updateWthrStats(e) {
    var oSelData = document.getElementById('select_data');
    var oSelType = document.getElementById('select_type');

    var aMovingObjects = new Array();
    var oMites = new oDust();


    if (oSelType.value == "hourly") {
        var oSelLen = document.getElementById('select_hour');
        var oSelDayDiv = document.getElementById('sel_day_div');
        var oSelHourDiv = document.getElementById('sel_hour_div');
        oMites.addMite('sel_day_div', 0);
        oMites.addMite('sel_hour_div', 1);
        /*if(addClass(oSelDayDiv,'hidden')){
         removeClass(oSelHourDiv,'hidden');
         aMovingObjects.push(oSelDayDiv);
         aMovingObjects.push(oSelHourDiv);

         }*/
    } else if (oSelType.value == "none") {
        oMites.addMite('sel_day_div', 0);
        oMites.addMite('sel_hour_div', 0);
        oMites.addMite('sel_pop_div', 0);
        oMites.addMite('sel_data_div', 0);
        oMites.addMite('wthr_output', 0);


        /*document.getElementById('sel_day_div').className="hidden";
         document.getElementById('sel_hour_div').className="hidden";
         document.getElementById('sel_pop_div').className="hidden";
         document.getElementById('sel_data_div').className="hidden";
         document.getElementById('wthr_output').className="hidden";
         */
        testRug.sweep(oMites);
        return false;
    }

    else {
        var oSelLen = document.getElementById('select_day');
        oMites.addMite('sel_day_div', 1);
        oMites.addMite('sel_hour_div', 0);
        /*document.getElementById('sel_hour_div').className="hidden";
         document.getElementById('sel_day_div').className="";*/
    }

    oMites.addMite('sel_data_div', 1);
    //document.getElementById('sel_data_div').className=""


    var dataString = "selData=" + oSelData.value + "&selType=" + oSelType.value + "&selLen=" + oSelLen.value;


    if (oSelData.value == 'Precipitation') {
        var oSelPop = document.getElementById('select_pop');
        oMites.addMite('sel_pop_div', 1);
        //document.getElementById('sel_pop_div').className="";
        dataString += "&selPop=" + oSelPop.value;
    } else {
        oMites.addMite('sel_pop_div', 0);
        //document.getElementById('sel_pop_div').className="hidden";

    }


    $.ajax({
        type: "POST",
        url: siteurl + 'weather/fetchWthrStats',
        data: dataString,
        success: function (result) {
            document.getElementById('wthr_output').innerHTML = result;
            oMites.addMite('wthr_output', 1);
            testRug.sweep(oMites);
            //document.getElementById('wthr_output').className="";
        },
        error: function () {
            alert('Something has gone horribly wrong. Sorry. I don\'t even know what it could have been.');
        }
    });


}
// newMessage event handler
function raiseandLower(e) {
    /* e here is the event
     e.srcElement is the element on which triggered the change
     e.detail.rug is the element which should raise/lower
     */


    var oSideBarList = e.detail.rug.classList;

    oSideBarList.add("raised");
    window.setTimeout(function () {
        oSideBarList.remove('raised');
    }, 1200)

}
function toggleClass(oObject, sClass) {
    /*sClass is toggled on the element defined in oObject(it's removed it existed, or it's added if hadn't
     If it was added, it returns true, if removed it returns false;
     */

    var bContainsClass = oObject.classList.contains(sClass);
    oObject.classList.toggle(sClass);

    return !bContainsClass;


}
function addClass(oObject, sClass) {
    /*
     sClass is added to the list objects. If it hadn't existed previously it adds the class and returns true
     if the class was there already, it returns false;
     */
    var bContainsClass = oObject.classList.contains(sClass);
    oObject.classList.add(sClass);

    return !bContainsClass;
}
function removeClass(oObject, sClass) {
    /*sClass is removed from oObject. If it existed and was removed, the removeClass returns, if it was not a part of
     the class, it returns false.
     Always trims the classList of white space
     */
    var bContainsClass = oObject.classList.contains(sClass);
    oObject.classList.remove(sClass);
    ;

    return bContainsClass;
}
function getBorders(pointer) {
    /*
     var pointer: either a string or object
     takes as input an object or id string
     returns an object with the borders as defined by the offSet properties
     */

    oObject = (typeof(pointer) === "string") ? document.getElementById(pointer) : pointer;

    oBorders = new Object();
    oBorders.left = oObject.offsetLeft;
    oBorders.right = oBorders.left + oObject.offsetWidth;
    oBorders.top = oObject.offsetTop;
    oBorders.bottom = oBorders.top + oObject.offsetHeight;

    return oBorders;

}
function parseCSSTime(sTime) {
    /*
     var sTime STRING
     a time value with "s" at the end to represent seconds
     */
    iTime = sTime.substr(0, sTime.length - 1);

    return parseFloat(iTime);
}
var oRug = function (direction, id) {
    /*'direction': defines the direction the text moves  to hide under the rug
     'id': the id of  the element which acts as the rug.
     */

    this.direction = direction;
    this.rug = document.getElementById(id);
    this.borders = getBorders(this.rug);
    this.raise = function () {
        var oSideBarList = this.rug.classList;
        oSideBarList.add("raised");
    }
    this.lower = function () {
        var oSideBarList = this.rug.classList;
        oSideBarList.remove('raised');
    }
    this.isRaised = function () {
        var oSideBarList = this.rug.classList;
        return oSideBarList.contains("raised");
    }
    this.sweep = function (oDust) {
        /*
         aDust should be the array returned by oDust.rArray();

         */
        var sRaisedClass = 'rug_Visible';
        var sLoweredClass = 'rug_Hidden';

        aDust = oDust.rArray();
        //var sideLocation =  this.rug
        for (var i = 0; i < aDust.length; ++i) {
            oCurrent = aDust[i];

            if ((oCurrent.visible && removeClass(oCurrent, sLoweredClass)) || (!oCurrent.visible && removeClass(oCurrent, sRaisedClass))) {
                if (!this.isRaised()) {
                    this.raise();
                }
                if (oCurrent.visible) {
                    //show element
                    addClass(oCurrent.obj, sRaisedClass);
                    oCurrent.obj.style.cssText = "";
                } else {
                    //hide it
                    addClass(oCurrent.obj, sLoweredClass);
                    if (this.direction == 'left') {
                        oCurrent.obj.style.left = this.borders.left - oCurrent.obj.offsetWidth;
                    }
                }
            }
        }
        var tempThis = this; //I hate this, is this common practice?
        var lowerTime = oDust.maxTransTime * 1000;
        window.setTimeout(function () {
            tempThis.lower();
        }, lowerTime);

    }
}
var oDust = function () {
    /*
     var id ID OF OBJECT TO BE SWEPT
     var visible BOOLEAN

     */
    this.addMite = function (pushID, pushVIS) {

        var CurObj = document.getElementById(pushID);

        var sTransDur = getComputedStyle(CurObj).transitionDuration;
        var sTransDel = getComputedStyle(CurObj).transitionDelay;
        var fTotalTime = parseCSSTime(sTransDur) + parseCSSTime(sTransDel);

        if (fTotalTime > this.maxTransTime) {
            this.maxTransTime = fTotalTime;
        }

        var oAddition = {'obj': CurObj, 'visible': pushVIS, 'borders': getBorders(pushID), 'totTransTime': fTotalTime, 'classList': CurObj.classList};
        this.fullArray.push(oAddition);
        return oAddition;
    }
    this.rArray = function () {
        return this.fullArray;
    }

    this.maxTransTime = 0;
    this.fullArray = Array();
    //this.addMite(id,visible);

}


//Main Code to run on document load.
testRug = new oRug('left', 'sidebar');


if ((oSelData = document.getElementById('select_data')) != null) {
    oSelData.onchange = updateWthrStats;
    document.getElementById('select_type').onchange = updateWthrStats;
    document.getElementById('select_hour').onchange = updateWthrStats;
    document.getElementById('select_day').onchange = updateWthrStats;
    document.getElementById('select_pop').onchange = updateWthrStats;

}

/*
 if ((oSideBar = document.getElementById('sidebar')) != null) {
 var raiseSidebar1 = new CustomEvent("fromBelow", {
 detail: {
 rug: oSideBar
 },
 bubbles: true,
 cancelable: true
 });


 document.addEventListener("fromBelow", raiseandLower, false);

 }
 */





