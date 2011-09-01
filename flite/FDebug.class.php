<?php
/**
 * Created on 2010-05-15
 *
 * @author Karol T.
 * 
 */
 
 class FDebug
 {
 	private $_errors = array();
 	private $_warnings = array();
 	private $_notices = array();
 	
 	private $_dumps = array();
 	private $_globalDumps = array();
 	
 	public function dumpVar($var, $txt = null)
 	{
 		$dump['value'] = $var;
 		$dump['desc'] = $txt;
 		
 		$call = debug_backtrace();
 		foreach ($call as $key => $row)
 		{
 			if ($row['function'] != 'dumpVar')
 			{
 				$idx = $key;
 				break;
 			}
 		}
 		$dump['file'] = substr($call[$idx]['file'], (strlen(ROOT_URI) + (strlen($_SERVER['SCRIPT_FILENAME'])-strlen($_SERVER['SCRIPT_NAME'])) - 7));
 		$dump['line'] = $call[$idx]['line'];
 		
 		$this->_dumps[] = $dump; 
 	}
 	
 	public function dumpGlobals()
 	{
 		$this->dumpVar($_GET);
 		$this->dumpVar($_POST);
 		$this->dumpVar($_SERVER);
 	}
 	
 	public function getDumps()
 	{
 		return $this->_dumps;
 	}
 	
 	public function getHTML()
 	{
 		$html = "<div id='debug'>" .
 				"<div id='debug_links'><a href='.' id='debug_show'>debug</a></div>" .
 				"<div id='debug_info'>" .
 				"<table>";
 		foreach ($this->_dumps as $dump)
 		{
 			//$html .= "<tr><td>test</td><td>test2</td></tr>";
 			$html .= sprintf("<tr><td>%s:%d</td><td>%s</td><td><pre>%s</pre></td></tr>", $dump['file'], $dump['line'], ($dump['desc'] ? $dump['desc'] : ''), print_r($dump['value'], true));
 		}
 		$html .= "</table>" .
 				"</div>" .
 				"</div>";

 		return $html;	
 	}
 	
 }
?>
