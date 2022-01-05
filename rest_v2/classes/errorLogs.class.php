<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Formatter\LineFormatter;
class ErrorLogs{
    public $log = '';
    public $dateFormat = "Y n j, g:i a";
    public $output = "%datetime% > %level_name% > %message%\n";
    public $formatter = '';
    public $stream = '';
    public function apiLogs($type, $msg){
		$this->log = new Logger('Guidedoc');
        $this->formatter = new LineFormatter($this->output, $this->dateFormat);
		if($type == 'error'){
            $this->stream = new StreamHandler($_SERVER['DOCUMENT_ROOT'].'errors.txt', Logger::WARNING);
            $this->stream->setFormatter($this->formatter);
			$this->log->pushHandler($this->stream);
			$this->log->error($msg);
		}else if($type == 'info'){
            $this->stream = new StreamHandler($_SERVER['DOCUMENT_ROOT'].'errors.txt', Logger::DEBUG);
            $this->stream->setFormatter($this->formatter);
			$this->log->pushHandler($this->stream);
			$this->log->pushHandler(new FirePHPHandler());
			$this->log->info($msg);
		}
	}
}


?>