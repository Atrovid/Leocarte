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
            lecturer_id CHAR(100) NOT NULL,
            room_id CHAR(100) NOT NULL,
            subject_id CHAR(100) NOT NULL,
            start_hour TIME(0) NOT NULL,
            finish_hour TIME(0) NOT NULL
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
            course_id CHAR(100) NOT NULL,
            student_id CHAR(100) NOT NULL,
            attending BOOLEAN NOT NULL
            );'
        ];
        foreach($statements as $statement){
            $this->pdo->exec($statement);
        }
        echo "InitAttendancesDB done \r\n";
    }

    public function dropTable($nom) {
        $query = "DROP TABLE ".$nom;
        $this->pdo->exec($query);
    }
}



?>