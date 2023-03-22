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
                        /* Le codeEtudiant est directement assigné pour notre base de donnée
                        Il s'agit donc d'une ligne à supprimer lors de la mise en place sur une autre bdd que le bac à sable*/
                        $codeEtudiant = "A00081";
                        $present = putPresent($codeStudent, $nameRoom);
                    } else {
                        return "Error";
                        die;
                    }
                } else {
                    return "Error";
                    die;
                }
            } else if($_GET['action'] == 'presentForm'){
                $studentID = (string)getInfoFromPresentForm();
                $nameRoom ="C-301";
                $present = putPresent($studentID, $nameRoom);
            }else{
                echo "L'action n'est pas connue";
                die;
            }
        } else {
            return "Error";
        }

    ?>
</html>
