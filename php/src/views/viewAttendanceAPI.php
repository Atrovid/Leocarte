<!DOCTYPE html>
<html lang="fr">

<head>
  <title>Class attendance</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>
  <div class="container-fluid p-5 bg-primary text-white text-center">
    <h1>Class Attendance</h1>
  </div>
  <div class="container-fluid mt-3">
    <div class="row">
      <div id="information" class="col-sm-4 p-3 bg-primary text-white">
        <h2>Information</h2>
        <form method="post" >
	        <input type="submit" class="btn btn-light" name="inCSV" value="Export csv"/>
        </form>
      </div>
      <div class="col-sm-8 p-3">
        <div id="students" class="table table-striped">
          <thead>
            <tr>
              <th>Students</th>
              <th>Presence</th>
            </tr>
          </thead>
        </div>
      </div>
    </div>
  </div>
</body>

</html>