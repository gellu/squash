<?php

class FLog
	{
		
		const OUTPUT_STDOUT = 1;
		const OUTPUT_FILE = 2;
		
		private $_logLevel = null;
		private $_logOutput = null;
		private $_logFile = null;
		private $_logFileAggregate = null;
		
		public function setLogLevel ($level)
		{
			$this->_logLevel = $level; 
		}
		
		public function setLogOutput ($output)
		{
			$this->_logOutput = $output;
		}
		
		public function setLogFile ($file, $aggregate)
		{
			$this->_logFile = str_replace ('.log', '', $file);
			$this->_logFileAggregate = $aggregate;
		}
		
		private function _getSeverityName ($severity)
		{
			$n = 'LOG_UNKNOWN';
			switch( $severity ){
				case LOG_EMERG:		$n = 'LOG_EMERG';	break;
				case LOG_ALERT:		$n = 'LOG_ALERT';	break;
				case LOG_CRIT:		$n = 'LOG_CRIT';	break;
				case LOG_ERR:		$n = 'LOG_ERR';		break;
				case LOG_WARNING:	$n = 'LOG_WARN';	break;
				case LOG_NOTICE:	$n = 'LOG_NOTICE';	break;
				case LOG_INFO:		$n = 'LOG_INFO';	break;
				case LOG_DEBUG:		$n = 'LOG_DEBUG';	break;
			}
			return sprintf( ' %-6s', $n );
		}
		
		public function log ($message, $severity)
		{
			if ($severity <= $this->_logLevel) {
				$str = '['. date ('Y-m-d H:i:s', time()) .']'. $this->_getSeverityName($severity) .' : '. $message;
	
				if ($this->_logOutput&self::OUTPUT_STDOUT) {
					echo $str ."\n"; 
				}
				
				if ($this->_logOutput&self::OUTPUT_FILE && isset($this->_logFile)) {
					$aggregate = '';
					switch ($this->_logFileAggregate) {
						case 'd': $aggregate = date ('dmY', time()); 	break;
						case 'm': $aggregate = date ('mY', time()); 	break;
						case 'y': $aggregate = date ('Y', time()); 		break;
						default : $aggregate = null; break;
					}
					$path = $this->_logFile . ($aggregate ? '_' : '') . $aggregate . '.log';
					//if (!file_exists($path))
						
					file_put_contents($this->_logFile . ($aggregate ? '_' : '') . $aggregate . '.log', $str . "\n", FILE_APPEND);					
				}
				
			}

		}
		

	}
?>