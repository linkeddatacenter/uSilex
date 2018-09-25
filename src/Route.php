<?php
namespace uSILEX;


Class Route 
{
    protected $verb;
    protected $path;
    protected $action;
    
    public function __construct( string $verb, string $path, string $action) 
    {    
        $this->verb = strtoupper($verb);
        $this->path = $path;
        $this->action = $action;
    }
    
    
    public function getHttpVerb() :string
    {
        return $this->verb;
    }
  
    
    public function getPath() :string
    {
        return $this->path;
    }
    
    
    public function getAction() :string
    {
        return $this->action;
    }
}