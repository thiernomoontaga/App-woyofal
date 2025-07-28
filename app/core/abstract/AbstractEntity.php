<?php 
namespace  fathie\Core\Abstract;


abstract class AbstractEntity {
    public function __construct(array $data = [])
    {
        // Constructeur de base qui peut être étendu par les classes filles
    }

    abstract public static function toObject($array):static ;

    abstract public function toArray():array;
    
    public function toJson():string{
        return json_encode($this->toArray()) ;
    }
}