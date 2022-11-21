<?php



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


    public function initDatabase() {
        $statements = [
            'CREATE TABLE IF NOT EXISTS students(
            student_id SERIAL PRIMARY KEY,
            first_name CHAR(100) NOT NULL,
            last_name CHAR(100) NOT NULL
            );'
        ];
        foreach($statements as $statement){
            $this->pdo->exec($statement);
        }
    }





}










?>