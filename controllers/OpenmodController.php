<?php

class OpenmodController {
  public function getAction($request) {
    $action = (isset($request->url_elements[2]) && !empty($request->url_elements[2])) ? $request->url_elements[2] : false;
    if($action == 'list') {
      $files = scandir('/home/minecraft/minecraft');
      $openmod = array();
      foreach($files as $file) {
        if(false !== strpos($file,'.jar'))
          array_push($openmod,$file);
      }
      return array('openmod' => $openmod);
    } else {
      $result = shell_exec("cat /home/minecraft/.minecraft | grep SERVICE_NAME | cut -d'=' -f 2");
      return array('openmod' => (!$result) ? 'Unknown' : trim($result));
    }
  }
  public function postAction($request) {
    if(isset($request->parameters['openmod']) && !empty($request->parameters['openmod']))
      $new = $request->parameters['openmod'];
    else
      return $request->error(400,'Please choose an OpenMod.');
    $old = $this->getAction();
    $new = str_replace('.jar', '', $new);
    shell_exec("sed -i 's/".$old['openmod']."/".$new."/g' /home/minecraft/.minecraft");
    return array('openmod' => $new);
  }
}