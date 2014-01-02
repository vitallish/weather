<!DOCTYPE HTML>
<html>

<head>
    <link type="text/css" rel="stylesheet" href="./../../css/soc.css">

</head>

<body>
<div id="board_container" class="border">
    <?php echo $nice ?>

</div>
<div id="system-message" class="right">

</div>
<div id="options" class="right">
    <label class="description" for="element_1">Game ID </label>

    <div>
        <input id="element_1" name="element_1" type="text" maxlength="255" value="1"/>
    </div>
    <p class="guidelines" id="guide_1">
        <small>Enter gameid to work with (should be 1)</small>
    </p>

    <label for="element_2">Stage </label>

    <div>
        <select id="element_2" name="element_2">
            <option value="1" selected="selected">Setup</option>
            <option value="2">Play</option>

        </select>
    </div>

    <label class="description" for="element_3">Play as Player: </label>

    <div>
        <select class="element select medium" id="element_3" name="element_3">
            <option value="1" elected="selected">1 - Yellow</option>
            <option value="2">2 - White</option>
            <option value="3">3 - Black</option>
            <option value="4">4 - Green</option>
        </select>
    </div>
    <div id="p_road" class="placemarker" onclick=placeRoad()>Place Road</div>
    <div id="p_settlement" class="placemarker" onclick=placeSettlement()>Place Settlement</div>
    <div id="p_cancel" class="placemarker" onclick=removePlaceability()>Cancel Placement</div>
    <div id="p_longest" class="placemarker" onclick=highlightLongestRoad()>Highlight Longest Road</div>
</div>

<div id="footer">
    <script src="./../../js/vSoc.js"></script>
</div>
</body>
</html>