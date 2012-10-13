<?php

class StatusController {

  public function getAction()
  {
    $df = shell_exec("df -h | grep xvda2");
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
        'free' => $match[3].'G',
        'percent' => $match[4].'%',
        ),
      'mem' => array(
        'total' => round($mem[1]/1024),
        'used'  => round($mem[2]/1024),
        'free'  => round(($mem[2] + $mem[3] + $mem[4])/1024),
        ),
      'cpu' => trim($cpu),
    );
  }
}