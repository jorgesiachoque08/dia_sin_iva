<?php
// Validador Params
/* $AuthController */
$app->get(
  '/api/obtener_dsi',
  function() use ($AuthController,$DescuentosSinImpuestosController) {
    $user = $AuthController->verificarToken();
    if ($user) {
      return json_encode($DescuentosSinImpuestosController->obtener_dsi());
    }
  }
);

$app->post(
  '/api/validarProducto/{cod_tercero}/{cod_mp}',
  function($cod_tercero,$cod_mp) use ($AuthController,$DescuentosSinImpuestosController,$request,$validador) {
    $user = $AuthController->verificarToken();
    if ($user) {
      if($request->getJsonRawBody()){
        $_POST = (array)$request->getJsonRawBody();
      }
      
      $validador->setRequeridos(["productos"]);
      $message = ["message" => [
          "productos" => "productos es requerida"
      ]];
      $validador->setMsjCamposRequerid($message);
      if($validador->Validando($_POST) === true){
        return json_encode($DescuentosSinImpuestosController->validarProducto($cod_tercero,$cod_mp,$_POST));
      }

    }
  }
);

$app->post(
  '/api/agregarCompraProducto',
  function() use ($AuthController,$DescuentosSinImpuestosController,$request,$validador) {
    $user = $AuthController->verificarToken();
    if ($user) {
      if($request->getJsonRawBody()){
        $_POST = (array)$request->getJsonRawBody();
      }
      
      $validador->setRequeridos(["object_id_tercero"]);
      $message = ["message" => [
          "object_id_tercero" => "object_id_tercero es requerida",
          "object_id_factura" => "object_id_factura es requerida",
          "cod_producto" => "cod_producto es requerida",
          "cantidad" => "cantidad es requerida"
      ]];
      $validador->setMsjCamposRequerid($message);
      if($validador->Validando($_POST) === true){
        return json_encode($DescuentosSinImpuestosController->agregarCompraProducto($_POST));
      }

    }
  }
);