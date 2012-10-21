<?php

class ConfigController {
  public function getAction($request) {

      $result = file('/home/minecraft/minecraft/server.properties',FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      $result = array_filter($result, function($line) {
        return !preg_match('/^#/', $line);
      });
      $config = array();
      foreach($result as $line) {
        $ex = explode('=',$line);
        $config[$ex[0]] = $ex[1];
      }
      return $config;
  }

  public function postAction($request) {
    if(0 == count($request->parameters))
      return $request->error(400,'Please send values to change.');

    foreach($request->parameters  as $key => $value)
    {
      $key = str_replace('_','.',$key);
      $result = shell_exec("grep -e '^".$key."=' /home/minecraft/minecraft/server.properties");
      if($result)
        shell_exec("sed -i 's/\(".$key."=\).*/\\1".$value."/g' /home/minecraft/minecraft/server.properties");
      else
        shell_exec("sed -i '$ a\\".$key."=".$value."' /home/minecraft/minecraft/server.properties");
    }

    return $this->getAction();
  }
}