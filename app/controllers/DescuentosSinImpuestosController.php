<?php

use Phalcon\Mvc\Controller;
use App\Models\Categorias;
class DescuentosSinImpuestosController extends Controller {

    public function obtener_dsi()
    {
        try{
            $sql = "SELECT 
            cod
            FROM descuentos_sin_impuestos dsi
            where current_timestamp() between CONCAT(dsi.fecha_inicial, ' ', dsi.hora_inicial) and CONCAT(dsi.fecha_final, ' ', dsi.hora_final);";
            $data = $this->db->fetchAll($sql);
            $codigo = 200;
            if(){
                
            }
            $this->response->setJsonContent(array(
                "code"=>$codigo,
                "message" => "Ok",
                "data" =>$data
            ));
        } catch (Exception $ex) {
            $codigo = 500;
            $this->response->setJsonContent(array(
                "code"=>$codigo,
                "message" => 'Error en el servidor',
                "data"=>false
            ));
        }
        $this->response->setStatusCode($codigo);
        $this->response->send();
    }

   

}