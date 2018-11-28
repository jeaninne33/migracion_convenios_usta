<?php


namespace TM;

//use PDO;

class Mysqlcheck
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct()
    {
        $connectionInsert = new ConnectionMySQL();
        $this->pdo = $connectionInsert->connect();
    }

    public function checkUsers($datos_id){
        try {
            $sql="SELECT id FROM users WHERE datos_personales_id=$datos_id;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result=$stmt->fetchAll();
            if(count($result)>0){
                return $result;
            }else{
                return 0;//array('error'=>'100','desc'=>"No existe EL usuario");
            }
        } catch (\PDOException $exception) {
            print_r($exception->getMessage());
        }

    }
   
    public function checkFacultadCampus($campus_id){
        try {
            //$sql="SELECT id FROM facultad WHERE campus_id=$campus_id;";
            $sql="SELECT id FROM division WHERE campus_id=$campus_id;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result=$stmt->fetchAll();
            if(count($result)>0){
                return $result;
            }else{
                return 0;//array('error'=>'100','desc'=>"No existe EL usuario");
            }
        } catch (\PDOException $exception) {
            print_r($exception->getMessage());
        }

    }
    public function checkAplication($name){
        try {
            $sql="SELECT id FROM aplicaciones WHERE nombre= '$name';";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result=$stmt->fetchAll();
            if(count($result)>0){
                return $result;
            }else{
                return 0;//array('error'=>'100','desc'=>"No existe EL usuario");
            }
        } catch (\PDOException $exception) {
            print_r($exception->getMessage());
        }

    }
    public function checkfacultad($name, $campus_id){
        try {
           // $sql="SELECT id FROM facultad WHERE nombre= '$name' and campus_id=$campus_id;" ;
            $sql="SELECT id FROM division WHERE nombre= '$name' and campus_id=$campus_id;" ;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result=$stmt->fetchAll();
            if(count($result)>0){
                return $result;
            }else{
                return 0;//array('error'=>'100','desc'=>"No existe EL usuario");
            }
        } catch (\PDOException $exception) {
            print_r($exception->getMessage());
        }

    }
    public function checkprograma($name,$campus_id){
        try {
          //  $sql="SELECT programa.id as id FROM programa  inner join facultad ON programa.facultad_id=facultad.id  where facultad.campus_id=$campus_id and programa.nombre= '$name';";
            $sql="SELECT  id FROM facultad   where campus_id=$campus_id and nombre= '$name';";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result=$stmt->fetchAll();
            if(count($result)>0){
                return $result;
            }else{
                return 0;//array('error'=>'100','desc'=>"No existe EL usuario");
            }
        } catch (\PDOException $exception) {
            print_r($exception->getMessage());
        }

    }

    public function checkAlianza($codigo,$fechaI){
        try {
            $sql="SELECT id FROM alianza WHERE codigo='$codigo' and fecha_inicio='$fechaI'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result=$stmt->fetchAll();
            if(count($result)>0){
                return $result;
            }else{
                return 0;//array('error'=>'100','desc'=>"No existe EL usuario");
            }
        } catch (\PDOException $exception) {
            print_r($exception->getMessage());
        }

    }
    
    public function checkProgramafacultad($campus_id){
        try {
            //$sql="SELECT programa.id as id FROM programa inner join facultad ON programa.facultad_id=facultad.id  where facultad.campus_id=$campus_id;";
            $sql="SELECT id FROM facultad where campus_id=$campus_id;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result=$stmt->fetchAll();
            if(count($result)>0){
                return $result;
            }else{
                return 0;//array('error'=>'100','desc'=>"No existe EL usuario");
            }
        } catch (\PDOException $exception) {
            print_r($exception->getMessage());
        }

    }

    public function checkTipoModalidad($name){
        try {
            $sql="SELECT id FROM tipo_modalidad WHERE nombre  LIKE '%".$name."%' and tipo=0 or tipo=2;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result=$stmt->fetchAll();
            if(count($result)>0){
                return $result;
            }else{
                return 0;// array('error'=>'102','desc'=>"No existe la modalidad en la BD");
            }
        } catch (\PDOException $exception) {
            print_r($exception->getMessage());
        }
    }
    public function checkCountry($name){
        try {
            $sql="SELECT id FROM pais WHERE nombre='".$name."'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result=$stmt->fetchAll();
            if(count($result)>0){
                return $result;
            }else{
                return 0;// array('error'=>'102','desc'=>"No existe la modalidad en la BD");
            }
        } catch (\PDOException $exception) {
            print_r($exception->getMessage());
        }
    }

    public function checkInstitution($name){
        try {
            $sql="SELECT id FROM institucion WHERE nombre LIKE '%".$name."%';";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $user=$stmt->fetchAll();
            if(count($user)>0){
                return $user;
            }else{
                return 0;
            }
        } catch (\PDOException $exception) {
            print_r($exception->getMessage());
        }
    }
    
    public function checkCampus($name){
        try {
            $sql="SELECT id FROM campus WHERE nombre LIKE '%".$name."%';";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result=$stmt->fetchAll();
            if(count($result)>0){
                return $result;
            }else{
                return 0;
            }
        } catch (\PDOException $exception) {
            print_r($exception->getMessage());
        }
    }


}