<!DOCTYPE html>
<html>
    <?php
        require_once('src/controllers/controller.php');
        require_once('src/controllers/requestTagCSN.php');


        if (isset($_GET['action']) && $_GET['action'] !== '') {
            if($_GET['action'] == 'curl' ) {
                if (isset($_GET['csn']) && $_GET['nameRoom']){
                    $csn=$_GET['csn'];
                    $nameRoom =$_GET['nameRoom'];
                    $codeStudent = logCSN($csn);
                    if ($codeStudent !== "ERROR"){
                        $present = putPresent($codeStudent, $nameRoom);
                    } else {
                        return "Error";
                        die;
                    }
                } else {
                    return "Error";
                    die;
                }
            }        
            else{
                echo "L'action n'est pas connue";
                die;
            }

        } else {
            return "Error";
        }

    ?>
</html>
