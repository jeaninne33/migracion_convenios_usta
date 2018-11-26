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
    public function checkUserProgram($user_id,$programa_id){
        try {
            $sql="SELECT id FROM user_programa WHERE user_id=$user_id and programa_id=$programa_id;";
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
    public function checkUserCampus($user_id,$campus_id){
        try {
            $sql="SELECT id FROM user_campus WHERE user_id=$user_id and campus_id=$campus_id;";
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
    public function checkUsersDatos($ci){
        try {
            $sql='SELECT id FROM datos_personales WHERE numero_documento= "'.$ci.'";';
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
    public function checkfacultad($name){
        try {
            $sql="SELECT id FROM facultad WHERE nombre= '$name'";
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
    public function checkprograma($name){
        try {
            $sql="SELECT id FROM programa WHERE nombre= '$name';";
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
    
    public function checkPeriod($name){
        try {
            $sql="SELECT id, fecha_desde, fecha_hasta FROM periodo WHERE nombre='".$name."';";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result=$stmt->fetchAll();
            if(count($result)>0){
                return $result;
            }else{
                return 0; //return array('error'=>'101','desc'=>"No existe EL periodo en la BD");
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
    
    public function checkModalidad($periodo_id, $institucion_id, $modalidad_id){
        try {
            $sql="SELECT id FROM modalidad WHERE periodo_id=$periodo_id and institucion_id=$institucion_id and tipo_modalidad_id=$modalidad_id";
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

      
    public function checkfuenteFinanciacion($name){
        try {
            $sql="SELECT id FROM fuente_financiacion WHERE nombre like '%$name%' ";
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
      
    public function checkFinanciacion($fuente_financiacion_id, $inscripcion_id){
        try {
            $sql="SELECT id FROM financiacion WHERE fuente_financiacion_id=$fuente_financiacion_id and inscripcion_id=$inscripcion_id  ";
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
    public function firstCampus($institucion_id){
        try {
            $sql="SELECT id FROM campus WHERE institucion_id=$institucion_id  limit 1;";
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