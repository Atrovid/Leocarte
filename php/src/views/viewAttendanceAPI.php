<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Class attendance</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link href="static/css/style.css" rel="stylesheet" type="text/css" />
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3"
        crossorigin="anonymous"></script>
</head>

<body class="p-3 m-0 border-0 bd-example">
    <header>
        <h1 id="title">Class attendance</h1>
    </header>
    <p>Bonjour, bienvenue sur cette nouvelle page</p>
    <?php
        foreach($response as $key => $value) {
            echo "{$key} => {$value}";
        }
        foreach($coursId as $key => $value) {
            echo "{$key} => {$value}";
        }
    ?>
    <div id="coucou"></div>
</body>

</html>