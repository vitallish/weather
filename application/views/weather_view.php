
<body>

    <div>
<!--   This div contains the selector for either hourly or daily predictions-->
        <?php
            echo $select_type;
        ?>
    </div>
    <div>
<!--   Contains selection for length of prediction-->
        <?php
            echo $sel_hour;

        echo $sel_day;
        ?>
    </div>
    <div>
<!--   Look at predictions for either temperature or precipitation     -->
        <?php
        echo $sel_data;
        ?>
    </div>
    <div>
<!--Select the predicted pop-->
    </div>
    <div id="wthr_output">


    </div>

</body>