<?php

use Phalcon\Mvc\Controller;
use App\Models\Categorias;
class DescuentosSinImpuestosController extends Controller {

    public function obtener_dsi()
    {
        try{
            $sql = "SELECT 
            dsi.cod
            FROM descuentos_sin_impuestos dsi
            where current_timestamp() between CONCAT(dsi.fecha_inicial, ' ', dsi.hora_inicial) and CONCAT(dsi.fecha_final, ' ', dsi.hora_final)";
            $data = $this->db->fetchAll($sql);

            if(count($data) > 0){
                $codigo = 200;
                $mensaje = "Ok";
                $retorno = $data[0]["cod"];
            }else{
                $codigo = 404;
                $mensaje = "No se encontraron resultados";
                $retorno = null;

            }
            $this->response->setJsonContent(array(
                "code"=>$codigo,
                "message" =>$mensaje,
                "data" =>$retorno
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

    public function validarProducto($cod_tercero,$cod_mp,$params)
    {
        try{
            $productos = json_decode($params["productos"]);
            if($productos){
                if(count($productos) > 0){
                    $sql = "SELECT 
                        dsi.*
                        FROM descuentos_sin_impuestos dsi
                        where current_timestamp() between CONCAT(dsi.fecha_inicial, ' ', dsi.hora_inicial) and CONCAT(dsi.fecha_final, ' ', dsi.hora_final)";
                    $dsi = $this->db->fetchAll($sql);

                    if(count($dsi) > 0){
                        $productoValidador = [];
                        $ctrl = true;
                        $sql = "select 
                                    p.cod
                                from
                                    descuentos_sin_impuestos dsi
                                    inner join limites_param_fisc lpf on lpf.cod=dsi.cod_lim_pf
                                    inner join detalles_lim_param_fisc dlpf on dlpf.cod_lim_pf =lpf.cod
                                    inner join valores_parametros_fiscales vpf on vpf.cod_parametro_fiscal=lpf.cod
                                    inner join descuentos_sin_impuesto_mp dsimp on dsimp.cod_descuento_sin_impuesto=dsi.cod
                                    inner join productos p on p.cod_categoria=dlpf.cod_categoria
                                    inner join medios_pagos mp on dsimp.cod_medio_pago=mp.cod
                                where
                                    mp.object_id='".$cod_mp."'
                                    and dsi.cod = ".$dsi[0]["cod"]."
                                    and p.cod in ";
                        $sqlCodProductos = "(";
                        foreach ($productos as $value) {
                            //&& (isset($value->cantidad) && is_numeric($value->cantidad) && $value->cantidad > 0 )
                            if(isset($value->cod_producto)){
                                // if (isset($productoValidador[$value->cod_producto])) {
                                //     $productoValidador[$value->cod_producto]->cantidad = $productoValidador[$value->cod_producto]->cantidad+$value->cantidad;
                                // }else{
                                //     $productoValidador[$value->cod_producto] = $value;
                                //     $sqlCodProductos .= $value->cod_producto.",";
                                // }
                                if (!isset($productoValidador[$value->cod_producto])) {
                                    $productoValidador[$value->cod_producto] = $value;
                                    $sqlCodProductos .= $value->cod_producto.",";
                                }
                            }else{
                                $ctrl = false;
                                break;
                            }

                        }

                        if($ctrl){
                            $sqlCodProductos = substr($sqlCodProductos,0,-1);
                            $sql = $sql.$sqlCodProductos.")";
                            $productosSinImp = $this->db->fetchAll($sql);
                            if(count($productosSinImp) > 0){

                                $sqlCodProductos = "";
                                $sql = "SELECT 
                                    tdsi.object_id_tercero,
                                    tdsi.cod_descuento_sin_impuesto,
                                    tdsi.cod_producto,
                                    sum(tdsi.cantidad) as cantidad
                                FROM terceros_descuentos_sin_impuestos as tdsi
                                where tdsi.object_id_tercero = '".$cod_tercero."' and tdsi.cod_producto in (";
                                foreach ($productosSinImp as $value) {
                                    $sqlCodProductos .= $value["cod"].",";
                                }
                                $sqlCodProductos = substr($sqlCodProductos,0,-1);
                                $sql = $sql.$sqlCodProductos;
                                $sql.= ") and tdsi.cod_descuento_sin_impuesto = ".$dsi[0]["cod"]."
                                GROUP BY tdsi.object_id_tercero,tdsi.cod_descuento_sin_impuesto,tdsi.cod_producto";
                                $data = $this->db->fetchAll($sql);
                                $arrayRetorno = [];
                                foreach ($productosSinImp as $value) {
                                    $ctrl = false;
                                    foreach ($data as $value2) {
                                        if($value["cod"] == $value2["cod_producto"]){
                                            $arrayRetorno[] = array("cod_producto"=>$value["cod"],"cant_disponible"=>$dsi[0]["nro_articulos_tercero"]-$value2["cantidad"]);
                                            $ctrl = true;
                                            break;
                                        }
                                    }
                                    if($ctrl == false){
                                        $arrayRetorno[] = array("cod_producto"=>$value["cod"],"cant_disponible"=>(int)$dsi[0]["nro_articulos_tercero"]);
                                    }
                                    
                                }
                                
                                $codigo = 200;
                                $mensaje = "Ok";
                                $retorno = $arrayRetorno;

                            }else{
                                $codigo = 404;
                                $mensaje = "No se encontraron resultados";
                                $retorno = null;
                
                            }
                            
                        }else{
                            $codigo = 400;
                            $mensaje = "parametro productos no tiene los campos requeridos o sus valores son incorrectos";
                            $retorno = [];
                        }
                    }else{
                        $codigo = 404;
                        $mensaje = "No se encontraron resultados";
                        $retorno = null;

                    }
                    
                }else{
                    $codigo = 400;
                    $mensaje = "parametro  produtos vacio";
                    $retorno = [];
                }
                
            }else{
                $codigo = 400;
                $mensaje = "parametro productos invalido";
                $retorno = [];
            }
            
            $this->response->setJsonContent(array(
                "code"=>$codigo,
                "message" =>$mensaje,
                "data" =>$retorno
            ));
        } catch (Exception $ex) {
            $codigo = 500;
            $this->response->setJsonContent(array(
                "code"=>$codigo,
                "message" => 'Error en el servidor',
                "data"=>$ex->getMessage()
            ));
        }
        $this->response->setStatusCode($codigo);
        $this->response->send();
    }

}