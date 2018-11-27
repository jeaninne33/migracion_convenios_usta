<?php


namespace TM;
use TM\Mysqlcheck;
use TM\Inserts;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class Convenios
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct()
    {
        $connection = new ConnectionMySQL();
        $this->pdo = $connection->connect();

    }

    /**
     * @return array|string
     */

    public function fetchAllconvenio($worksheet){

        try {
            
            // create a log
            $log = new Logger('Files');

            $formatter = new LineFormatter(null, null, false, true);
            $infoHandler = new StreamHandler('info.log', Logger::INFO, false);
            $infoHandler->setFormatter($formatter);

            $errorHandler = new StreamHandler('error.log', Logger::ERROR);
            $errorHandler->setFormatter($formatter);

            // This will have messages
            $log->pushHandler($infoHandler);
            // This will have only ERROR messages
            $log->pushHandler($errorHandler);
            //se instancian las clases
            $check=new Mysqlcheck();//para validar si existe se instancia la clase
            $inserts=new Inserts();//para validar si existe se instancia la clase
            $countInsert=0;
            $countError=0;
            $highestRow = $worksheet->getHighestRow();//total de registros filas de la hoja
            $tabla="<label class='text-success'>DATA INSERT</label><br /><table class='table table-bordered'>";
            echo  $tabla; 
            date_default_timezone_set('America/Bogota');
            $fecha_hoy=date("Y-m-d h:i:s");
            $existe= "<label class='text-success'></label><br />";
            for($row=5; $row<=$highestRow; $row++)//se recorre todo el archivo excel  
            {
                $error=0;
                $output='';
                $campus =preg_replace("([^a-zA-ZñÑáéíóúÁÉÍÓÚ\s\W])", '',trim($worksheet->getCellByColumnAndRow(0, $row)->getValue(), " \t\n\r\0\x0B"));//campus del convenio
                $codigo =trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());//codigo del convenio
                $pais=preg_replace("([^a-zA-ZñÑáéíóúÁÉÍÓÚ\s\W])", '',trim($worksheet->getCellByColumnAndRow(2, $row)->getValue(), " \t\n\r\0\x0B"));//,'utf-8');// pais donde se encuentra la universidad 
                $universidad =  preg_replace("([^a-zA-ZñÑáéíóúÁÉÍÓÚ\s\W])", '',trim($worksheet->getCellByColumnAndRow(3, $row)->getValue(), " \t\n\r\0\x0B"));//,'utf-8');//la institución con que esta el convenio
                $objeto =  trim($worksheet->getCellByColumnAndRow(4, $row)->getValue());//objetivo del convenio
                $vigencia = trim($worksheet->getCellByColumnAndRow(5, $row)->getValue());//vigencia
                $duracion =  trim($worksheet->getCellByColumnAndRow(6, $row)->getValue());//duracion del convenio
                $fechaI =  trim($worksheet->getCellByColumnAndRow(7, $row)->getValue());//fecha inicio del convenio
                $tituloC =  trim($worksheet->getCellByColumnAndRow(8, $row)->getValue());//titulo del convenio
                $tipo =   trim($worksheet->getCellByColumnAndRow(9, $row)->getValue());//tipo del  convenio ya sea marco o especifico
                $aplicaciones =  trim($worksheet->getCellByColumnAndRow(10, $row)->getValue());//aplicaciones de acuerdo al tipo
                $programas =  trim($worksheet->getCellByColumnAndRow(11, $row)->getValue());//programas beneficiados
                $facultades = trim( $worksheet->getCellByColumnAndRow(12, $row)->getValue());//falcultades beneficiarias

                if(!empty($universidad) && !empty($pais) && !empty($objeto) && !empty($duracion) && !empty($fechaI) && !empty($tituloC) && !empty($tipo) 
                && !empty($facultades)){// && !empty($aplicaciones)
                    //se calcula la fecha fin del convenio
                    if($fechaI!='Sin fecha'){
                        if($duracion=='Indefinida'){
                            $duracion=10;
                        }
                        $fechaF=date("Y-m-d",strtotime($fechaI."+ $duracion year"));
                    }else{
                        $fechaF='';
                        $fechaI='';
                    }
                    $this->pdo->beginTransaction();
                    // if(!empty($codigo)){
                    //     $checkAlianza = $check->checkAlianza($codigo,$fechaI);
                    //     if($checkAlianza!=0){
                    //         $msj="Fila excel No. $row ; el convenio ya existe con el codigo ; codigo del convenio; $codigo  universidad; $universidad ";
                    //         $log->error("  \r\n".$msj." \r\n");
                    //         goto end;
                    //     }
                    // }
                    //se busca el campus donde se creo el convenio
                    $checkCampus=$check->checkCampus($campus);
                    if ($checkCampus==0) {// si no existe el campus

                    }else{
                        $campus_id=$checkCampus[0]['id'];
                    }
                    if(!isset($campus_id)){
                        $msj="Fila excel No. $row ; error no existe el campus ; codigo del convenio; $codigo  universidad; $universidad; campus; $campus";
                        $log->error("  \r\n".$msj." \r\n");
                        $output.='<tr><td>';
                        $output.= $msj;
                        $output.='</td></tr>';
                        $error++;
                        goto end;
                    }
                    if($codigo=='Sin Código'){
                        $codigo='';
                    }
                     //se inserta el convenio
                    $sql="INSERT INTO alianza (codigo, objetivo, tipo_tramite_id, duracion, estado_id, fecha_inicio, campus_id, fecha_fin, migration, created_at) values ('$codigo','$objeto',1,'$duracion AÑOS',3,'$fechaI',$campus_id,'$fechaF',1,' $fecha_hoy' );";
                    //se valida el pais
                    $alianza_id=$inserts->InsertGeneral($sql);
                    if(!isset($alianza_id) || empty($alianza_id)){
                        $error++;
                        goto end;
                    }
                    $countInsert++;
                    $log->info("  \r\n".'Convenio Insertado con exito; id; '. $alianza_id." ; universidad; $universidad ; # inserción; $countInsert  \r\n");
                    $output.='<tr><td>';
                    $output.="Convenio Insertado con exito; id; $alianza_id ; universidad ; $universidad; # inserción; $countInsert";
                    $output.='</td></tr>';
                    $checkCountry = $check->checkCountry($pais);
                    if ($checkCountry==0) {// si no existe el pais
                        $pais=mb_strtoupper($pais, 'utf-8');
                        //se prepatra la data para la creacion del pais
                        $sql="INSERT INTO pais (nombre, nacionalidad, created_at ) values ('".$pais."','$pais','$fecha_hoy');";
                        $pais_id=$inserts->InsertGeneral($sql);
                        if(!isset($pais_id) || empty($pais_id)){
                            $error++;
                            goto end;
                        }
                        $log->info("  \r\n".'Pais Insertado con exito; id; '. $pais_id." ; nombre_pais; $pais  \r\n");
                        $output.='<tr><td>';
                        $output.="Pais Insertado con exito; id; $pais_id ; nombre_pais ; $pais ";
                        $output.='</td></tr>';
                    } else {// si existe el periodo
                        $pais_id=$checkCountry[0]['id'];
                    } 
                     //se valida la institucion con la que se hizo el convenio
                     $checkinstitucion = $check->checkInstitution($universidad);
                     if ($checkinstitucion==0) {// si no existe la institucion_destino
                         //se prepatra la data para la creacion de la institucion
                         $universidad=mb_strtoupper($universidad, 'utf-8');
                         $sql="INSERT INTO institucion (nombre, tipo_institucion_id, migration,	pais_id, created_at) values ('".$universidad."',7, 1,$pais_id,'$fecha_hoy');";
                         $institucion_id=$inserts->InsertGeneral($sql);
                         if(!isset($institucion_id) || empty($institucion_id)){
                            $error++;
                            goto end;
                         }
                         $log->info("  \r\n".'Institucion Insertada con exito; id; '. $institucion_id."; nombre;  $universidad  \r\n");
                         $output.='<tr><td>';
                         $output.="Institucion Insertada con exito; id; '. $institucion_id";
                         $output.='</td></tr>';
                     } else {// si existe la institucion
                         $institucion_id=$checkinstitucion[0]['id'];
                     }
                    //se inserta la relacion entre la alianza y la institución sede
                    $sql="INSERT INTO alianza_institucion (alianza_id, institucion_id, created_at)  values ($alianza_id,1,'$fecha_hoy');";
                    $alianza_institucionsede_id=$inserts->InsertGeneral($sql);
                    if(!isset($alianza_institucionsede_id) || empty($alianza_institucionsede_id)){
                        $error++;
                        goto end;
                     }
                     $log->info("  \r\n".'alianza_institucion  sede insertado con exito; id; '. $alianza_institucionsede_id."  \r\n");
                     $output.='<tr><td>';
                     $output.="alianza_institucion sede insertado con exito; id; '. $alianza_institucionsede_id.";
                     $output.='</td></tr>';
                     //se inserta la relacion de la alianza con la instirucion que se hace el convenio
                    $sql="INSERT INTO alianza_institucion (alianza_id, institucion_id, created_at)  values ($alianza_id,$institucion_id, '$fecha_hoy');";
                    $alianza_institucion_id=$inserts->InsertGeneral($sql);
                    if(!isset($institucion_id) || empty($institucion_id)){
                        $error++;
                        goto end;
                     }
                    $log->info("  \r\n"."alianza_institucion  $universidad insertado con exito; id; ". $alianza_institucion_id."  \r\n");
                    $output.='<tr><td>';
                    $output.="alianza_institucion  $universidad insertado con exito; id; ". $alianza_institucion_id;
                    $output.='</td></tr>';
                    if(!isset($alianza_institucion_id) || empty($alianza_institucion_id)){
                        $error++;
                        goto end;
                     }
                    //se valida la facultad en caso de usta la division si existe
                    $facultades=explode("\n", $facultades);
                    foreach($facultades as $facultad){
                         $facultad=mb_strtoupper($facultad, 'utf-8');
                        $checkfacultad = $check->checkfacultad($facultad);//se verifica si existe la facultad
                        if ($checkfacultad==0) {
                            $msj="Fila excel No. $row ; no se encontro la division; $facultad ";
                            $log->error("  \r\n".$msj." \r\n");
                            $output.='<tr><td>';
                            $output.=  $msj;
                            $output.='</td></tr>';// si no existe la facultad
                       
                            //se prepatra la data para la creacion de la facultad
                            // $sql="INSERT INTO facultad  ( nombre, campus_id, tipo_facultad_id, created_at)  values ('".$facultad."',1,1,'$fecha_hoy');";
                            // $facultad_id=$inserts->InsertGeneral($sql);
                            // if(!isset($facultad_id) || empty($facultad_id)){
                            //     goto end;
                            //  }
                            // $log->info("  \r\n"."facultad insertada con exit; id; $facultad_id ; nombre; $facultad"."  \r\n");
                            // $output.='<tr><td>';
                            // $output.="facultad insertada con exit; id; $facultad_id ; nombre; $facultad";
                            // $output.='</td></tr>';
                        } else {// si existe la facultad
                            foreach($checkfacultad as $facul){
                                $facultad_id=$facul['id'];//se almacena el id
                                 //se inserta la relacion de la facultad con la alianza
                                $sql="INSERT INTO alianza_facultad (alianza_id, facultad_id)  values ($alianza_id,$facultad_id);";
                                $alianza_facultad_id=$inserts->InsertGeneral($sql);
                                if(!isset($alianza_facultad_id) || empty($alianza_facultad_id)){
                                    $error++;
                                    goto end;
                                }
                                $log->info("  \r\n"."alianza_facultad insertado con exito; id;  $alianza_facultad_id; nombre facultad; $facultad   \r\n");
                                $output.='<tr><td>';
                                $output.="alianza_facultad  insertado con exito; id;  $alianza_facultad_id; nombre; $facultad";
                                $output.='</td></tr>';
                            }
                        }
                    }//fin foreach 

                    //se valida el programa en caso de usta la facultad
                    $programas=explode(",", $programas);
                    foreach($programas as $programa){
                        $programa=mb_strtoupper($programa, 'utf-8');
                        $checkprograma = $check->checkprograma($programa);//se verifica si existe la programa
                        if ($checkprograma==0) {// si no existe la programa
                            $msj="Fila excel No. $row ; no se encontro el programa; $programa ";
                            $log->error("  \r\n".$msj." \r\n");
                            $output.='<tr><td>';
                            $output.=  $msj;
                            $output.='</td></tr>';// si no existe la programa
                            //se prepatra la data para la creacion de la programa
                            // $sql="INSERT INTO programa  (id, nombre, facultad_id, created_at)  values ('".$programa."',1,1,'$fecha_hoy');";
                            // $programa_id=$inserts->InsertGeneral($sql);
                            // if(!isset($programa_id) || empty($programa_id)){
                            //     goto end;
                            // }
                            // $log->info("  \r\n"."facultad insertada con exit; id; $programa_id ; nombre; $programa"."  \r\n");
                            // $output.='<tr><td>';
                            // $output.="facultad insertada con exit; id; $programa_id ; nombre; $programa";
                            // $output.='</td></tr>';
                        } else {// si existe la facultad
                            $programa_id=$checkprograma[0]['id'];//se almacena el id
                            foreach($checkprograma as $program){
                                $facultad_id=$facul['id'];//se almacena el id
                                 //se inserta la relacion de la facultad con la alianza
                                //se inserta la relacion de la programa con la alianza
                                $sql="INSERT INTO alianza_programa (alianza_id,  programa_id)  values ($alianza_id,$programa_id);";
                                $alianza_programa_id=$inserts->InsertGeneral($sql);
                                if(!isset($alianza_programa_id) || empty($alianza_programa_id)){
                                    $error++;
                                    goto end;
                                }
                                $log->info("  \r\n"."alianza_programa insertado con exito; id;  $alianza_programa_id; nombre facultad; $programa   \r\n");
                                $output.='<tr><td>';
                                $output.="alianza_programa  insertado con exito; id;  $alianza_programa_id; nombre; $programa";
                                $output.='</td></tr>';
                            }
                        }
                       
                    }//fin foreach 
                    //se valida de acuerdo al dato el id de la aplicacion
                    if($aplicaciones=='B. Movilidad Académica Estudiantil,'){
                        $aplicacion_id=4;
                    }elseif($aplicaciones=='1. Cooperación Interinstitucional'){
                        $aplicacion_id=1;
                    }elseif($aplicaciones=='A. Prácticas y Pasantías,'){
                        $aplicacion_id=3;
                    }elseif($aplicaciones=='2. Actividades Científicas y de Cooperación Académica Investigativa'){
                        $aplicacion_id=2;
                    }
                    if (isset($aplicacion_id)) {// si no existe el pais
                        $pais=mb_strtoupper($pais, 'utf-8');
                        //se prepatra la data para la creacion del pais
                        $sql="INSERT INTO alianza_aplicaciones (alianza_id,  aplicaciones_id, created_at ) values ($alianza_id,$aplicacion_id,'$fecha_hoy');";
                        $alianza_aplicacion_id=$inserts->InsertGeneral($sql);
                        if(!isset($alianza_aplicacion_id) || empty($alianza_aplicacion_id)){
                            $error++;
                            goto end;
                        }
                        $log->info("  \r\n alianza_aplicaciones Insertado con exito; id;  $alianza_aplicacion_id ; nombre aplicacion; $aplicaciones  \r\n");
                        $output.='<tr><td>';
                        $output.="alianza_aplicaciones Insertado con exito; id;  $alianza_aplicacion_id ; nombre aplicacion; $aplicaciones";
                        $output.='</td></tr>';
                    } 
                    end:
                        $output.='<br>';
                        if($error>0){
                            $this->pdo->rollBack();
                        }else{
                            $this->pdo->commit();
                        }
                    echo  $output;                
                  
                }else{
                    $msj="Fila excel No. $row ; error campos vacios ; codigo del convenio; $codigo  universidad; $universidad ";
                    $log->error("  \r\n".$msj." \r\n");
                }  //fin si no esta vacia la fila 
            } //fin for 
            echo '</table>';
            return   true;

        } catch (\PDOException $exception) {
            $this->pdo->rollBack();
            return "Error ejecutando la consulta: " . $exception->getMessage().' - '.$exception->getLine();
        }
    }

}