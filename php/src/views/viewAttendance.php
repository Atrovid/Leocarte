<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Appel des élèves</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3"
        crossorigin="anonymous"></script>

</head>

<header>
    <h1 id="title">Présence en cours</h1>
</header>

<body class="p-3 m-0 border-0 bd-example">
    <div class="container-fluid mt-3" id="invisible">
        <div class="container-fluid">
            <div class="row">
                <div class="col-3 bg-info p-3">
                    <article id="informations">
                        <h2>Information sur le cours</h2>
                        <dl>
                            <dt>Course : </dt>
                            <dd>Maths</dd>
                            <dt>Heure de début</dt>
                            <dd>8h</dd>
                            <dt>Heure de fin</dt>
                            <dd>9h</dd>
                        </dl>
                    </article>
                    <button type="button" class="btn btn-outline-dark">Convert to a file</button>
                </div>
                <div class="col-9 p-3">
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Attending</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($results as $result){
                                    if ($result['attending']){
                                        $attending = "Yes";
                                    } else {
                                        $attending = "No";
                                    }
                                    $row = "<tr>
                                    <td>".$result['first_name']."</td>
                                    <td>".$result['last_name']."</td>
                                    <td>".$attending."</td>
                                    </tr>";
                                    echo $row;
                                } 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>