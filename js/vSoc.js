var oTestPlayers = {
    "1": {"color": "yellow"},
    "2": {"color": "white"},
    "3": {"color": "black"},
    "4": {"color": "green"}
};

function getPlayer() {
    var iPlayer = document.getElementById('element_3').value;
    var sColor = oTestPlayers[iPlayer].color;
    return {
        "pid": iPlayer,
        "color": sColor
    }
}
function getAdjHex(oHex) {
    var sIds = oHex.dataset.adjhex;
    return sIds.split(" ");


}
function removePlaceability() {
    //Run to clear board of all clickable objects
    var oAllPlaceable = document.querySelectorAll('.placeable');
    var iTotalPlaceable = oAllPlaceable.length;
    for (var i = 0; i < iTotalPlaceable; ++i) {
        oAllPlaceable[i].style.cursor = "";
        oAllPlaceable[i].style.boxShadow = "";
        oAllPlaceable[i].classList.remove("placeable");
        oAllPlaceable[i].onclick = null;
    }
}
function highlightLongestRoad() {
    var oPlayer = getPlayer();
    var oAllRoadsByPID = document.querySelectorAll('.road.taken.pid' + oPlayer.pid);
    var iTotalRoads = oAllRoadsByPID.length;
    for (var j = 0; j < iTotalRoads; ++j) {
        var oCurrRoad = oAllRoadsByPID[j];
        console.log(allPaths([oCurrRoad]));
        console.log(oCurrRoad);


    }
}
function breakRoad(sRoadID) {
    sHexes = sRoadID.split("_")[1];
    return sHexes.split('-');

}
function joinRoad(aHexIds) {
    aHexIds.sort();
    return 'road_' + aHexIds.join("-");
}
function isRoadBranch(oRoadCheck, oRoad1, oRoad2) {
    if (oRoad2 === undefined) {
        return false;
    }
    var aHexRoad1 = breakRoad(oRoad1.id);
    var aHexRoad2 = breakRoad(oRoad2.id);
    var iIndex;
    var iCommon;
    //Which hex to they have in common?
    if (aHexRoad1.indexOf(aHexRoad2[0]) == -1) {
        //the common hex is in aHexRoad2[1]
        iCommon = 1;
    } else {
        //the common hex is in aHexRoad2[0]
        iCommon = 0;
    }
    var testHex1 = aHexRoad2[1 - iCommon];
    iIndex = aHexRoad1.indexOf(aHexRoad2[iCommon]); //iIndex is now where the hex is in common
    var testHex2 = aHexRoad1[1 - iIndex]; //we want to make the test hex the other one
    return joinRoad([testHex1, testHex2]) == oRoadCheck.id


}
function allPaths(aRoadList) {
    var oCurrRoad = aRoadList[aRoadList.length - 1];
    var aAdjRoads = roadNearRoad(oCurrRoad);
    var aFinal = [];
    var pid = 'pid1'; //only look at player 1 so far
    for (var i = 0; i < aAdjRoads.length; ++i) {
        var oCurAdj = aAdjRoads[i];
        var oBranchCheck = aRoadList[aRoadList.length - 2];
        var bValidRoad = (aRoadList.indexOf(oCurAdj) == -1) && (oCurAdj.classList.contains(pid)) && (!isRoadBranch(oCurAdj, oCurrRoad, oBranchCheck))
        if (bValidRoad) {
            //we found a new adjacent road
            var tempRoadlist = aRoadList.slice();
            tempRoadlist.push(oCurAdj);
            var fullPath = allPaths(tempRoadlist);
            //once it hits this point, we already have the [[array1][array2]]
            for (var j = 0; j < fullPath.length; ++j) {
                aFinal.push(fullPath[j]);
            }
        }
    }
    if (aFinal.length != 0) {
        return aFinal;
    } else {
        return [aRoadList];
    }
}

function boardSetup() {
    var oBoardContainer = document.getElementById('board_container');
    var oHexes = oBoardContainer.childNodes;
    var oBoardBound = oBoardContainer.getBoundingClientRect();
    var iTotalTiles = oHexes.length;


    for (var i = 1; i < (iTotalTiles - 1); ++i) {
        var oCurHex = oHexes[i];
        if (oCurHex.classList.contains('land')) {
            sAdjHex = oCurHex.dataset.adjhex;
            aAdjHex = sAdjHex.split(" ");
            for (j = 0; j < aAdjHex.length; ++j) {
                boundHex = oCurHex.getBoundingClientRect();
                iLeft = boundHex.left - oBoardBound.left;
                iTop = boundHex.top - oBoardBound.top;

                tempRoad = document.createElement('div');
                aRoadID = [oCurHex.id, aAdjHex[j]];
                aRoadID.sort();
                tempRoad.id = 'road_' + aRoadID.join('-');

                tempSett = document.createElement('div');
                if (j != 5) {
                    aSettID = [oCurHex['id'], aAdjHex[j], aAdjHex[j + 1]];
                } else {
                    aSettID = [oCurHex['id'], aAdjHex[j], aAdjHex[0]];
                }
                aSettID.sort();
                tempSett.id = 'sett_' + aSettID.join('-');

                switch (j) {
                    case 0:
                        tempRoad.style.top = (iTop - 9.5) + "px";
                        tempRoad.style.left = (iLeft + 17) + "px";
                        tempRoad.className = "road forward empty";

                        tempSett.style.top = (iTop - 9) + "px";
                        tempSett.style.left = (iLeft + 44 - 11) + "px";
                        tempSett.className = "settlement empty";

                        break;
                    case 1:
                        tempRoad.style.top = (iTop - 9.5) + "px";
                        tempRoad.style.left = (iLeft + 61) + "px";
                        tempRoad.className = "road backward empty";

                        tempSett.style.top = (iTop + 25 - 11) + "px";
                        tempSett.style.left = (iLeft + 88 - 11) + "px";
                        tempSett.className = "settlement empty";

                        break;
                    case 5:
                        tempRoad.style.top = (iTop + 27) + "px";
                        tempRoad.style.left = (iLeft - 5) + "px";
                        tempRoad.className = "road empty";


                        tempSett.style.top = (iTop + 25 - 11) + "px";
                        tempSett.style.left = (iLeft - 11) + "px";
                        tempSett.className = "settlement empty";

                        break;
                    case 3:
                        tempRoad.style.top = (iTop + 65.5) + "px";
                        tempRoad.style.left = (iLeft + 61) + "px";
                        tempRoad.className = "road forward empty";

                        tempSett.style.top = (iTop - 9 + 98) + "px";
                        tempSett.style.left = (iLeft + 44 - 11) + "px";
                        tempSett.className = "settlement empty";

                        break;
                    case 4:
                        tempRoad.style.top = (iTop + 65.5) + "px";
                        tempRoad.style.left = (iLeft + 17) + "px";
                        tempRoad.className = "road backward empty";

                        tempSett.style.top = (iTop + 75 - 11) + "px";
                        tempSett.style.left = (iLeft - 11) + "px";
                        tempSett.className = "settlement empty";
                        break;
                    case 2:
                        tempRoad.style.top = (iTop + 27) + "px";
                        tempRoad.style.left = (iLeft - 5 + 88) + "px";
                        tempRoad.className = "road empty";

                        tempSett.style.top = (iTop + 75 - 11) + "px";
                        tempSett.style.left = (iLeft + 88 - 11) + "px";
                        tempSett.className = "settlement empty";

                        break;

                    default:
                }

                oRoadCheck = document.getElementById(tempRoad.id);
                if (!(typeof(oRoadCheck) != 'undefined' && oRoadCheck != null)) {
                    oBoardContainer.appendChild(tempRoad);
                }

                oSettCheck = document.getElementById(tempSett.id);
                if (!(typeof(oSettCheck) != 'undefined' && oSettCheck != null)) {
                    oBoardContainer.appendChild(tempSett);
                }

            }
        }

    }
}

function placeSettlement() {
    removePlaceability();
    if (document.getElementById('element_2').value == 1) {// this is the setup phase
        var oAllSettlements = document.querySelectorAll('.settlement.empty');
    } else {
        //it's the normal play phase, and settlements can only be placed where there are roads
        oPlayer = getPlayer();
        var oAllRoadsByPID = document.querySelectorAll('.road.taken.pid' + oPlayer.pid);
        var iTotalRoads = oAllRoadsByPID.length;

        var oAllSettlements = [];
        for (var j = 0; j < iTotalRoads; ++j) {
            var oCurrRoad = oAllRoadsByPID[j];
            var aPossSettlement = settlementNearRoad(oCurrRoad); //array of two settlements
            for (var k = 0; k < aPossSettlement.length; ++k) {
                if (aPossSettlement[k].classList.contains('empty') && oAllSettlements.indexOf(aPossSettlement[k]) == -1) {
                    oAllSettlements.push(aPossSettlement[k]);
                }
            }
        }

    }


    var iTotalSett = oAllSettlements.length;
    for (var i = 0; i < iTotalSett; ++i) {
        oCurrSett = oAllSettlements[i];
        bPlaceable = true; //Assume we can place there until proven otherwise

        sCurrId = oCurrSett.id;
        sCurrCorr = sCurrId.split('_')[1];
        aCurrCorr = sCurrCorr.split('-');
        //first pair of coordinates(0 and 1)
        var bT1 = bAdjSettlement(aCurrCorr, 0, 1, 2);
        var bT2 = bAdjSettlement(aCurrCorr, 1, 2, 0);
        var bT3 = bAdjSettlement(aCurrCorr, 2, 0, 1);

        if (bT1 && bT2 && bT3) {
            placeableSettlement(oCurrSett);
        }
    }
}
function settlementNearRoad(oCurrRoad) {
    var sCurrRoad = oCurrRoad.id.split('_')[1];
    var aHexes = sCurrRoad.split('-');

    var aAdjHex1 = getAdjHex(document.getElementById(aHexes[0]));
    var aAdjHex2 = getAdjHex(document.getElementById(aHexes[1]));
    var aSettlements = [];
    for (var j = 0; j < aAdjHex1.length; ++j) {
        var sTestHex = aAdjHex1[j];
        if (aAdjHex2.indexOf(sTestHex) > -1) {
            //found adjHex (should only be 2)
            var aSettlement = [aHexes[0], aHexes[1], sTestHex].sort();
            var oSettlement = document.getElementById('sett_' + aSettlement.join('-'));
            if (!(typeof(oSettlement) == 'undefined' || oSettlement == null)) {
                aSettlements.push(oSettlement);
            }
        }
    }
    //aSettlements should always have two members

    return aSettlements;


}
function bAdjSettlement(aCurrCorr, iAdjHex1, iAdjHex2, iCurHex) {
    var oadjHex1 = document.getElementById(aCurrCorr[iAdjHex1]);
    var oadjHex2 = document.getElementById(aCurrCorr[iAdjHex2]);

    var aAdjHex1 = getAdjHex(oadjHex1);
    var aAdjHex2 = getAdjHex(oadjHex2);

    for (var j = 0; j < aAdjHex1.length; ++j) {
        var sTestHex = aAdjHex1[j];
        if (aAdjHex2.indexOf(sTestHex) > -1) {
            //found adjHex
            if (aCurrCorr[iCurHex] != sTestHex) {//make sure we don't get the same trifecta
                aTestSettCorr = [aCurrCorr[iAdjHex1], aCurrCorr[iAdjHex2], sTestHex];
                aTestSettCorr.sort();
                sTestSettCorr = 'sett_' + aTestSettCorr.join('-');
                oTestSett = document.getElementById(sTestSettCorr)
                if (!(typeof(oTestSett) == 'undefined' || oTestSett == null)) {
                    //It is a valid settlement location
                    if (!oTestSett.classList.contains('empty')) {
                        //The adjacent settlement is taken, therefore you cannot place a settlement at sCurrId
                        bPlaceable = false;
                        break;
                    }
                }
            }
        }
    }
    return bPlaceable;

}
function placeableSettlement(oCurrSett) {
    oPlayer = getPlayer();

    oCurrSett.style.cursor = "pointer";
    oCurrSett.style.boxShadow = "1px 1px 1px " + oPlayer.color;
    oCurrSett.classList.add("placeable");
    oCurrSett.onclick = addSettlement;

}
function addSettlement() {
    oPlayer = getPlayer();

    this.style.backgroundColor = oPlayer.color;
    this.classList.remove('empty');
    this.classList.remove('placeable');
    this.classList.add('taken');
    this.classList.add('pid' + oPlayer.pid);

    this.onclick = null;
    this.style.cursor = "";
    removePlaceability();
}

function placeRoad() {
    removePlaceability();
    oPlayer = getPlayer();
    var oAllSettlements = document.querySelectorAll('.settlement.taken.pid' + oPlayer.pid);
    // return all settlements placed by the current player
    var iTotalSett = oAllSettlements.length;
    for (var i = 0; i < iTotalSett; ++i) {
        oCurrSett = oAllSettlements[i];
        //bPlaceable = true; //Assume we can place there until proven otherwise

        sCurrId = oCurrSett.id;
        sCurrCorr = sCurrId.split('_')[1];
        aCurrCorr = sCurrCorr.split('-');

        //check for possible roads near current settlements
        roadNearSettlement(aCurrCorr, 0, 1);
        roadNearSettlement(aCurrCorr, 0, 2);
        roadNearSettlement(aCurrCorr, 1, 2);
    }
    var oAllRoads = document.querySelectorAll('.road.taken.pid' + oPlayer.pid);
    var iTotalRoads = oAllRoads.length;

    for (var j = 0; j < iTotalRoads; ++j) {
        oCurrRoad = oAllRoads[j];
        var aAdjacentRoads = roadNearRoad(oCurrRoad); //always outputs all viable roads (max4 min2)
        for (var k = 0; k < aAdjacentRoads.length; ++k) {
            if (aAdjacentRoads[k].classList.contains('empty')) {
                placeableRoad(aAdjacentRoads[k]);
            }

        }

    }
}
function roadNearSettlement(aCurrCorr, iAdjHex1, iAdjHex2) {
    var aCurrRoad = [aCurrCorr[iAdjHex1], aCurrCorr[iAdjHex2]].sort();
    var roadID = 'road_' + aCurrRoad.join('-');
    var oRoad = document.getElementById(roadID);
    //check to see if road is taken and check to see
    if (!(typeof(oRoad) == 'undefined' || oRoad == null)) {
        //It is a valid road location
        if (oRoad.classList.contains('empty')) {
            //nobody has placed a road there yet
            placeableRoad(oRoad);
        }
    }
}
function roadNearRoad(oCurrRoad) {
    var sCurrRoad = oCurrRoad.id.split('_')[1];
    var aHexes = sCurrRoad.split('-');

    var aAdjHex1 = getAdjHex(document.getElementById(aHexes[0]));
    var aAdjHex2 = getAdjHex(document.getElementById(aHexes[1]));
    var aRoads = [];
    for (var j = 0; j < aAdjHex1.length; ++j) {
        var sTestHex = aAdjHex1[j];
        if (aAdjHex2.indexOf(sTestHex) > -1) {
            //found adjHex (should only be 2)
            var aRoad1 = [sTestHex, aHexes[0]].sort();
            var aRoad2 = [sTestHex, aHexes[1]].sort();
            var oRoad1 = document.getElementById('road_' + aRoad1.join('-'));
            var oRoad2 = document.getElementById('road_' + aRoad2.join('-'));
            if (!(typeof(oRoad1) == 'undefined' || oRoad1 == null)) {
                aRoads.push(oRoad1);
            }
            if (!(typeof(oRoad2) == 'undefined' || oRoad2 == null)) {
                aRoads.push(oRoad2);
            }
        }
    }
    //aRoads should now have only members that are real roads

    return aRoads;
}
function placeableRoad(oCurrRoad) {
    oPlayer = getPlayer();

    oCurrRoad.style.cursor = "pointer";
    oCurrRoad.style.boxShadow = "1px 1px 1px " + oPlayer.color;
    oCurrRoad.classList.add("placeable");
    oCurrRoad.onclick = addRoad;


}
function addRoad() {
    oPlayer = getPlayer();

    this.style.backgroundColor = oPlayer.color;
    this.classList.remove('empty');
    this.classList.remove('placeable');
    this.classList.add('taken');
    this.classList.add('pid' + oPlayer.pid);

    this.onclick = null;
    this.style.cursor = "";
    removePlaceability();


}

//Run on document load:
boardSetup();
