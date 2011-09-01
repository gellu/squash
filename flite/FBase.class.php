<?php
/**
 * podstawowe wyjatki frameworku
 * @author karol
 *
 */
class FBaseException extends Exception {
	
}

/**
 * Created on 2010-05-10
 *
 * @author Karol T.
 * 
 */
class FBase
{
	/**
	 * wrzuca zmienna do debuggera
	 * 
	 * @param mixed $var
	 * @param string $txt
	 */
	public function dump($var, $txt)
	{
		FLite::getInstance()->getDebugger()->dumpVar($var, $txt);
	}
	
}
?>
