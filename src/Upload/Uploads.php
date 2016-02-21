<?php
namespace Upload;

use RuntimeException;
use ArrayAccess;
use Iterator;
use JsonSerializable;

class Uploads implements ArrayAccess,Iterator,JsonSerializable
{
    protected $UploadGroups = [];

    public function __construct(){
        if( !isset($_FILES) || empty($_FILES) ){
            throw new RuntimeException('The $_FILES super-global array is empty; nothing to do');
        }
        //var_dump($_FILES);
        //reorder $_FILES into objects array
        foreach($_FILES as $namespace => $fileAttrGroup){
            $this->UploadGroups[$namespace] = new UploadGroup($fileAttrGroup);
        }
    }//\

    public function count(){
        $i = 0;
        foreach($this->UploadGroups as $group){
            $i += $group->count();
        }
        return $i;
    }//\

    public function __toString(){
        return json_encode($this);
    }

//-----------------------------------------ArrayAccess methods->
    public function offsetExists($offset){
        return isset($this->UploadGroups[$offset]);
    }//\

    public function offsetGet($offset){
        return isset($this->UploadGroups[$offset])? $this->UploadGroups[$offset] : null;
    }//\

    public function offsetSet($offset, $value){
        if( is_null($offset) ){
            $this->UploadGroups[] = $value;
        }
        else{
            $this->UploadGroups[$offset] = $value;
        }
    }//\

    public function offsetUnset($offset){
        unset($this->UploadGroups[$offset]);
    }//\

//-----------------------------------------Iterator methods->
    public function valid(){
        return isset($this->UploadGroups[key($this->UploadGroups)]);
    }

    public function current(){
        return current($this->UploadGroups);
    }

    public function next(){
        return next($this->UploadGroups);
    }

    public function rewind(){
        reset($this->UploadGroups);
    }

    public function key(){
        return key($this->UploadGroups);
    }

//---------------------------------------------JsonSerializable->
    public function jsonSerialize(){
        return $this->UploadGroups;
    }
}//\
