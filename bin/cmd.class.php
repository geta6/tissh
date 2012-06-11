<?php

class CMD extends CMDBASE {

  protected function help() {
    return array('html' => $this->reqhelp());
  }

  protected function services() {
    return array(
      'html' => implode(', ', array(
        '<a href="http://たい.jp">たい.jp</a>',
        '<a href="http://ない.jp">ない.jp</a>',
        '<a href="http://wp.geta6.net">Blog</a>',
        '<a href="http://ss.geta6.net">SlideScript</a>',
        '<a href="http://hy.geta6.net">HyperMemo.js</a>'
      ))
    );
  }

  protected function nyan() {
    return array('html' => '<img src="/img/nyan.gif"><audio src="/img/nyan.mp3" autoplay loop></audio>');
  }

  protected function api() {
    return array('html' => $this->reqhelp());
  }

  protected function clear() {
    return array(
      'text' => 'clear',
      'exec' => '$(".res, .req").remove();'
    );
  }

  protected function 就活() {
    return array('text' => '死ぬ');
  }

  protected function 留年() {
    return array('text' => 'true');
  }

  protected function 退学() {
    return array('text' => 'false');
  }

  protected function 童貞() {
    return $this->dt();
  }

  protected function kill() {
    return array('text' => 'そいつ殺せない');
  }

  protected function dt() {
    return array('text' => 'true');
  }

  protected function encstat () {
    exec('/usr/local/bin/encstat', $e);
    $e = preg_replace('|\[[0-9]+?m|', '', implode("\n", $e));
    return array('text' => $e);
  }

  protected function amazon() {
    return array(
      'text' => 'HDD買ってください',
      'exec' => 'window.open("http://amzn.to/MgplPc");'
    );
  }

  protected function open() {
    if (empty($this->arg)) {
      return $this->error();
    } else {
      switch ($this->arg[0]) {
        case 'tai':
          $uri = 'http://たい.jp/';
          break;
        case 'nai':
          $uri = 'http://ない.jp/';
          break;
        case 'hypermemo':
          $uri = 'http://hy.geta6.net/';
          break;
        case 'slidescript':
          $uri = 'http://ss.geta6.net/';
          break;
        default :
          $uri = 'http://' . $this->arg[0] . '.geta6.net/';
      }
    }
    return array(
      'text' => 'open ' . implode(',', $this->arg),
      'exec' => "window.open('$uri');"
    );
  }

}

class CMDBASE {

  protected $cmd = '';
  protected $opt = array();
  protected $arg = array();

  public function __construct($cmd) {
    $cmd = preg_replace('|  *|', ' ', $cmd);
    $cmd = explode(' ', $cmd);
    $this->cmd = array_shift($cmd);
    if (0 < count($cmd)) $this->interargs($cmd);
  }

  private function interargs ($array) {
    $set = false;
    foreach ($array as $key => $val) {
      if ($set) {
        if ('-' != substr($val, 0, 1)) {
          $this->opt[$get] = $val;
          $set = false;
          continue;
        } else {
          $set = false;
        }
      }
      if ('-' == substr($val, 0, 1)) {
        $get = str_replace('-', '', $val);
        $this->opt[$get] = true;
        $set = true;
      } else {
        $this->arg[] = $val;
      }
    }
  }

  protected function error ($text = false) {
    return array( 'text' => $this->cmd . ": missing args or invalid format detected.\n" . ($text?"$text\n":'') . "\nTry `" . $this->cmd . " --help` for more informations." );
  }

  protected function reqhelp () {
    $file = __DIR__ . '/help/' . $this->cmd;
    if (is_file($file)) {
      return file_get_contents($file);
    } else {
      return false;
    }
  }

  public function execute () {
    $reqhelp = $this->reqhelp();
    if ((isset($this->opt['help']) || isset($this->opt['h'])) && method_exists($this, $this->cmd) && $reqhelp) {
      $result = array('html' => $reqhelp);
    } elseif (method_exists($this, $this->cmd)) {
      $result = call_user_func_array(array($this, $this->cmd), array());
    } else {
      $text = 'tissh: command not found: ' . $this->cmd;
      $exec = 'javascript:void(0)';
      if (preg_match('/[<>]/', $this->cmd)) {
        $text .= "\nインターネット警察に通報しました";
      } else if (preg_match('/(ない|たい)\.jp$/', $this->cmd)) {
        if (preg_match('/\*/', $this->cmd)) {
          $text = "Asterisk is wildcard.\nEx. 愛のままにわがままに.僕は君だけを傷つけ.ない.jp";
        } else {
          $text = $this->cmd;
          $exec = 'window.open("http://' . $this->cmd . '")';
        }
      }
      $result = array('text' => $text, 'exec' => $exec);
    }
    return false !== $result ? json_encode($result) : json_encode($this->error());
  }

}

