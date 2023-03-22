<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Appel des élèves</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link href="static/css/style.css" rel="stylesheet" type="text/css" />
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3"
        crossorigin="anonymous"></script>

    <script src="static/js/script.js"></script>
</head>

<header>
    <h1 id="title">Présence en cours</h1>
</header>

<body class="p-3 m-0 border-0 bd-example">
    <form id="Formulaire" class="was-validated" method ="post" action="">
        <div class="mb-3">
            <label for="matiere_cours" class="form-label">Matière enseignée : </label>
            <input type="text" name="id_subject" class="form-control" id="matiere_cours" placeholder="Enter the id_subject of the course" required>
            <div class="valid-feedback">Valid</div>
            <div class="invalid-feedback">Please fill out this field</div>
        </div>
        <div class="row">
            <div class="col">
                <label for="horaire_debut" class="form-label">Horaire de début : </label>
                <input type="time" name="start_hour" class="form-control" id="horaire_debut" placeholder="Enter the begin of the course"
                    >
                <div class="valid-feedback">Valid</div>
                <div class="invalid-feedback">Please fill out this field</div>
            </div>
            <div class="col">
                <label for="horaire_fin" class="form-label">Horaire de fin : </label>
                <input type="time" name="end_hour" class="form-control" id="horaire_fin" placeholder="Enter the end of the course"
                    >
                <div class=" valid-feedback">Valid</div>
                <div class="invalid-feedback">Please fill out this field</div>
            </div>
        </div>
        <!--<button type="button" class="btn btn-primary" id="envoyer">Envoyer</button>-->
        <input type="submit" value="Envoyer">
    </form>

</body>

</html>
