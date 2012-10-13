<?php

class StatusController {

  public function getAction()
  {
    $df = shell_exec("df -h | grep disk");
    preg_match(
      "/([0-9\.]*)G\s*".
      "([0-9\.]*)G\s*".
      "([0-9\.]*)G\s*".
      "([0-9\.]*)%\s*/",
      $df,
      $match);
    $mem = shell_exec("cat /proc/meminfo | head -n 4");
    preg_match(
      "/^[A-Za-z:\s]*([0-9]*)\skB\n".
      "[A-Za-z:\s]*([0-9]*)\skB\n".
      "[A-Za-z:\s]*([0-9]*)\skB\n".
      "[A-Za-z:\s]*([0-9]*)\skB\n/",
      $mem,
      $mem);
    $cpu = shell_exec("cat /proc/loadavg | cut -d' ' -f1");
    return array(
      'disk' => array(
        'total' => $match[1].'G',
        'used' => $match[2].'G',
        'available' => $match[3].'G',
        'percent' => $match[4].'%',
        ),
      'mem' => array(
        'total' => $mem[1],
        'free'  => $mem[2] + $mem[3] + $mem[4],
        ),
      'cpu' => trim($cpu),
    );
  }
}