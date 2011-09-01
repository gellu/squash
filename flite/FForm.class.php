<?php
/**
* 
*/
class FForm
{
    private $_elements = array();
    private $_elTypes = array();
    public function addElement($type, $name, $params = array())
    {
//        $ind = count($this->_elements);
//        $el = array();
//        $this->_elTypes[$type]++;
//        $el['id'] = $name . '_id';
//        $el['name'] = $name;
//        
//        if ($type == 'text' || $type == 'pass' || $type == 'submit')
//            $el['value'] = "";
//        elseif ($type == 'radio')
//            $el['checked'] = ($params['checked'])?(true):(false);
//        elseif ($type == 'select')
//        {
//            if (is_array($params['options']))
//            {
//                //foreach ($params['options'] as $)
//            }
//            $el['options']
//        }
        
    }
    
    public function addRule(&$elem, $type, $params)
    {
        
    }
    
    public function addValidator(&$elem, $type, $params)
    {
        
    }
    
    public function addGroup($type)
    {
        
    }
    
    public function addGroupRule($type)
    {
        
    }
    
    public function setFilter(&$elem)
    {
        
    }
    
    public function setDefaults($data)
    {
        
    }
    
    public function render()
    {
        
    }
    
    public function validate()
    {
        
    }
    /*
    private function _getAttributes($type)
    {
        $attrs = array('id', 'name', 'value');
        
        
        return $attrs;
    }
    */
    
}


?>