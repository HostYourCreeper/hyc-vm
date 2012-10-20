<?php

class MessagesController {
  public function getAction($request) {
      $result1 = shell_exec("cat /home/minecraft/.minecraft | grep MSG_STOP | cut -d'=' -f 2");
      $result2 = shell_exec("cat /home/minecraft/.minecraft | grep MSG_REBOOT | cut -d'=' -f 2");
      return array(
        'stop' => trim($result1),
        'restart' => trim($result2)
      );
  }
  public function postAction($request) {
    $new = array('stop' => false, 'restart' => false);
    if(isset($request->parameters['stop']) && !empty($request->parameters['stop']))
      $new['stop'] = $request->parameters['stop'];
    if(isset($request->parameters['restart']) && !empty($request->parameters['restart']))
      $new['restart'] = $request->parameters['restart'];
    if(!$new['stop'] && !$new['restart'])
      return $request->error(400,'No messages specified.');
    $old = $this->getAction();
    if($new['stop']) {
      if(!$old['stop']) {
        shell_exec('echo \'MSG_STOP="HERE"\' >> /home/minecraft/.minecraft');
        $old['stop'] = '"HERE"';
      }
      shell_exec("sed -i 's/\(MSG_STOP=\).*/\\1".($new['stop'])."/g' /home/minecraft/.minecraft");
    } else
      $new['stop'] = $old['stop'];
    if($new['restart']) {
      if(!$old['restart']) {
        shell_exec('echo \'MSG_REBOOT="HERE"\' >> /home/minecraft/.minecraft');
        $old['restart'] = '"HERE"';
      }
      shell_exec("sed -i 's/\(MSG_REBOOT=\).*/\\1".($new['restart'])."/g' /home/minecraft/.minecraft");

    } else
      $new['restart'] = $old['restart'];
    return $new;
  }
}