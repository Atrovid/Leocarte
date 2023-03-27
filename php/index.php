<!DOCTYPE html>
<html>
    <body>
        <h1>Bienvenue sur la page de projet 2A</h1>
    <?php
        require_once('src/controllers/controller.php');
        require_once('src/controllers/requestTagCSN.php');


        if (isset($_GET['action']) && $_GET['action'] !== '') {
            if($_GET['action'] == 'attendance' ) {
                if (isset($_GET['csn']) && $_GET['room']){
                    $csn=$_GET['csn'];
                    $nameRoom =$_GET['room'];
                    $codeStudent = logCSN($csn);
                    if ($codeStudent !== "ERROR"){
                        /* Le codeEtudiant est directement assigné pour notre base de donnée
                        Il s'agit donc d'une ligne à supprimer lors de la mise en place sur une autre bdd que le bac à sable*/
                        $codeEtudiant = "A00082";
                        $present = putPresent($codeEtudiant, $nameRoom);
                        return $present;
                    } else {
                        return "Erreur";
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
                return $present;
            }else{
                echo "L'action n'est pas connue";
                die;
            }
        } else {
            echo "Pas d'action trouvée";
            return "Error";
        }

    ?>
    </body>
</html>
