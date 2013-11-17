<body>
<div id="sidebar">

</div>
<div class="rug_visible">
    <!--   This div contains the selector for either hourly or daily predictions-->
    <?php

    echo $select_type;

    ?>
</div>
<div id="sel_hour_div" class="rug_Hidden">
    <!--   Contains selection for length of prediction-->
    <?php
    echo $sel_hour;
    ?>
</div>
<div id="sel_day_div" class="rug_Hidden">
    <?php
    echo $sel_day;
    ?>
</div>
<div id="sel_data_div" class="rug_Hidden">
<!--   Look at predictions for either temperature or precipitation     -->
    <?php
    echo $sel_data;
    ?>
</div>

<div id="sel_pop_div" class="rug_Hidden">
<!--Select the predicted pop-->
    <?php
    echo $sel_pop;
    ?>
</div>

    <pre>
    <div id="wthr_output" class="rug_Hidden">


    </div>
    </pre>
</body>