<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rectification appel</title>
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


<body class="p-3 m-0 border-0 bd-example">
    <form id="Formulaire" class="was-validated" method="post" action="">
        <div class="mb-3">
            <label for="matiere_cours" class="form-label">Numéro étudiant de l'élève auquel vous voulez mettre présent : </label>
            <input type="text" name="studentID" class="form-control" id="matiere_cours" pattern="A\d{5}" placeholder="Entrer le numéro étudiant de l'élève (ex. A12345)" required>
            <div class="valid-feedback">Valide</div>
            <div class="invalid-feedback">Veuillez entrer une chaîne de caractères qui commence par 'A' suivie de 5 chiffres</div>
        </div>

        <input type="submit" value="Envoyer">
    </form>


</body>

</html>