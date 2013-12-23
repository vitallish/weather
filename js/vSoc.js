var oBoardContainer = document.getElementById('board_container');
var oHexes = oBoardContainer.childNodes;
var oBoardBound = oBoardContainer.getBoundingClientRect();
var iTotalTiles = oHexes.length;

for (i = 1; i < (iTotalTiles - 1); ++i) {
    oCurHex = oHexes[i];
    if (oCurHex.classList.contains('land')) {
        sAdjHex = oCurHex.dataset.adjhex;
        aAdjHex = sAdjHex.split(" ");
        for (j = 0; j < aAdjHex.length; ++j) {
            boundHex = oCurHex.getBoundingClientRect();
            iLeft = boundHex.left - oBoardBound.left;
            iTop = boundHex.top - oBoardBound.top;

            tempRoad = document.createElement('div');
            $aRoadID = [oCurHex.id, aAdjHex[j]];
            $aRoadID.sort();
            tempRoad.id = 'road_' + $aRoadID.join('-');

            tempSett = document.createElement('div');
            if (j != 5) {
                $aSettID = [oCurHex['id'], aAdjHex[j], aAdjHex[j + 1]];
            } else {
                $aSettID = [oCurHex['id'], aAdjHex[j], aAdjHex[0]];
            }
            $aSettID.sort();
            tempSett.id = 'sett_' + $aSettID.join('-');

            switch (j) {
                case 0:
                    tempRoad.style.top = (iTop - 9.5) + "px";
                    tempRoad.style.left = (iLeft + 17) + "px";
                    tempRoad.className = "road forward";

                    tempSett.style.top = (iTop - 9) + "px";
                    tempSett.style.left = (iLeft + 44 - 11) + "px";
                    tempSett.className = "settlement";

                    break;
                case 1:
                    tempRoad.style.top = (iTop - 9.5) + "px";
                    tempRoad.style.left = (iLeft + 61) + "px";
                    tempRoad.className = "road backward";

                    tempSett.style.top = (iTop + 25 - 11) + "px";
                    tempSett.style.left = (iLeft + 88 - 11) + "px";
                    tempSett.className = "settlement";

                    break;
                case 5:
                    tempRoad.style.top = (iTop + 27) + "px";
                    tempRoad.style.left = (iLeft - 5) + "px";
                    tempRoad.className = "road";


                    tempSett.style.top = (iTop + 25 - 11) + "px";
                    tempSett.style.left = (iLeft - 11) + "px";
                    tempSett.className = "settlement";

                    break;
                case 3:
                    tempRoad.style.top = (iTop + 65.5) + "px";
                    tempRoad.style.left = (iLeft + 61) + "px";
                    tempRoad.className = "road forward";

                    tempSett.style.top = (iTop - 9 + 98) + "px";
                    tempSett.style.left = (iLeft + 44 - 11) + "px";
                    tempSett.className = "settlement";

                    break;
                case 4:
                    tempRoad.style.top = (iTop + 65.5) + "px";
                    tempRoad.style.left = (iLeft + 17) + "px";
                    tempRoad.className = "road backward";

                    tempSett.style.top = (iTop + 75 - 11) + "px";
                    tempSett.style.left = (iLeft - 11) + "px";
                    tempSett.className = "settlement";
                    break;
                case 2:
                    tempRoad.style.top = (iTop + 27) + "px";
                    tempRoad.style.left = (iLeft - 5 + 88) + "px";
                    tempRoad.className = "road";

                    tempSett.style.top = (iTop + 75 - 11) + "px";
                    tempSett.style.left = (iLeft + 88 - 11) + "px";
                    tempSett.className = "settlement";

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