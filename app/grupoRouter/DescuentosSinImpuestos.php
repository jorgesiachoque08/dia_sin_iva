<?php
// Validador Params
/* $AuthController */
$app->get(
  '/api/obtener_dsi',
  function() use ($DescuentosSinImpuestosController,$request,$validador) {
    /* $user = $AuthController->verificarToken();
    if ($user) {; */
        
    //}

    if($request->getJsonRawBody()){
        $_GET = (array)$request->getJsonRawBody();
    }
    
    $validador->setRequeridos(["fecha_Actual"]);
    $message = ["message" => [
        "fecha_Actual" => "fecha_Actual es requerida"
    ]];
    $validador->setMsjCamposRequerid($message);
    $validador->setFechas(["fecha_Actual"]);
    $messageFechas = [
      "format" => [
        "fecha_Actual" => "Y-m-d h:m:s",
      ],
      "message" => [
      "fecha_Actual" => "fecha_Actual no es valida formt(Y-m-d)"
    ]];
    $validador->setMsjCamposFechas($messageFechas);

    if ($validador->Validando($_GET) === true) {
        return json_encode($DescuentosSinImpuestosController->listarCategorias($_GET));
    }
  }
);