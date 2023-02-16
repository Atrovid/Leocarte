<?php
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['inCSV']))
    {
        func();
    }
    function func()
    {
        echo "WELCOME";     
    }
?>
