<?php

ini_set('display_errors', 1);

class Model {
    /**
     * PDO instance
     * @var type
     */
    private $pdo;

    function __construct()
    {
        $this->pdo = $this->connect();
    }

    /**
     * return in instance of the PDO object that connects to the SQLite database
     * @return \PDO
     */
    public function connect() {
        if ($this->pdo == null) {
            $cf = parse_ini_file('config.ini');
            $dsn = "pgsql:host=".$cf['db_url'].";port=".$cf['db_port'].";dbname=".$cf['db_name'].";";
            $user = $cf['db_user'];
            $password = $cf['db_password'];
            // make a database connection
            try {
                $this->pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
        return $this->pdo;
    }


    public function initStudentsDB() {
        $statements = [
            'CREATE TABLE IF NOT EXISTS students(
            student_id SERIAL PRIMARY KEY,
            first_name CHAR(100) NOT NULL,
            last_name CHAR(100) NOT NULL,
            student_number CHAR(100) NOT NULL
            );'
        ];
        foreach($statements as $statement){
            $this->pdo->exec($statement);
        }
        echo "InitStudentDB done\r\n";
    }

    public function initClassesDB(){
        $statements = [
            'CREATE TABLE IF NOT EXISTS classes(
            class_id SERIAL PRIMARY KEY,
            lecturer_id INTEGER NOT NULL,
            room_id INTEGER NOT NULL,
            subject_id INTEGER NOT NULL,
            FOREIGN KEY (lecturer_id) REFERENCES lecturers(lecturer_id),
            FOREIGN KEY (room_id) REFERENCES rooms(room_id),
            FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
            start_hour TIME(0) NOT NULL,
            end_hour TIME(0) NOT NULL
            );'
        ];
        foreach($statements as $statement){
            $this->pdo->exec($statement);
        }
        echo "InitClasses done\r\n";
    }

    public function initRoomsDB(){
        $statements = [
            'CREATE TABLE IF NOT EXISTS rooms(
            room_id SERIAL PRIMARY KEY,
            room_name CHAR(100) NOT NULL
            );'
        ];
        foreach($statements as $statement){
            $this->pdo->exec($statement);
        }
        echo "InitRoomDB done\r\n";
    }

    public function initLecturersDB(){
        $statements = [
            'CREATE TABLE IF NOT EXISTS lecturers(
            lecturer_id SERIAL PRIMARY KEY,
            lecturer_first_name CHAR(100) NOT NULL,
            lecturer_last_name CHAR(100) NOT NULL
            );'
        ];
        foreach($statements as $statement){
            $this->pdo->exec($statement);
        }
        echo "InitLecturersDB done\r\n";
    }

    public function initSubjectsDB(){
        $statements = [
            'CREATE TABLE IF NOT EXISTS subjects(
            subject_id SERIAL PRIMARY KEY,
            subject_name CHAR(100) NOT NULL
            );'
        ];
        foreach($statements as $statement){
            $this->pdo->exec($statement);
        }
        echo "InitSujectsDB done\r\n";
    }


    public function initAttendancesDB(){
        $statements = [
            'CREATE TABLE IF NOT EXISTS attendances(
            class_id INTEGER NOT NULL,
            student_id INTEGER NOT NULL,
            FOREIGN KEY(class_id) REFERENCES classes(class_id),
            FOREIGN KEY(student_id) REFERENCES students(student_id),
            attending BOOLEAN NOT NULL
            );'
        ];
        foreach($statements as $statement){
            $this->pdo->exec($statement);
        }
        echo "InitAttendancesDB done \r\n";
    }

    public function dropTable($nom) {
        $query = "DROP TABLE IF EXISTS ".$nom;
        $this->pdo->exec($query);
    }

    public function addIntoStudentsDB($student_id, $first_name, $last_name, $student_number){
        $query = "INSERT INTO students (student_id, first_name, last_name, student_number) VALUES ('$student_id', '$first_name', '$last_name', '$student_number')";
        $this->pdo->query($query);
    }

    public function deleteIntoStudentDB($student_id){
        $query = "DELETE FROM students WHERE student_id = $student_id";
        $this->pdo->query($query);
    }

    public function displayStudentDB(){
        $query = "SELECT * FROM students";
        foreach($this->pdo->query($query) as $row){
            print "<br>";
            print $row['student_id'].'-'.$row['first_name'].'-'.$row['last_name'].'-'.$row['student_number'].'<br/>';
        }
    }

    public function addIntoLecturersDB($lecturer_id, $lecturer_first_name, $lecturer_last_name){
        $query = "INSERT INTO lecturers(lecturer_id, lecturer_first_name, lecturer_last_name) VALUES ('$lecturer_id', '$lecturer_first_name', '$lecturer_last_name')";
        $this->pdo->query($query);
    }

    
}

?>