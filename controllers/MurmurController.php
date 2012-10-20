<?php

class MurmurController {

  public function getAction($request)
  {
    $action = (isset($request->url_elements[2]) && !empty($request->url_elements[2])) ? $request->url_elements[2] : false;
    if(!$action)
      return $request->error(404,'What would you do with minecraft?');
    if(!in_array($action, array('start','stop','restart')))
      return $request->error(404,'You can\'t do that.');
    
    $result = shell_exec("/etc/init.d/minecraft ".$action);
    if(!$result)
      return $request->error(500,'An error occured while executing command.');
    else
      return $result;
  }

}