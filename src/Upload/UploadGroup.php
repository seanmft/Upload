<?php
namespace Upload;

use \Iterator;
use \ArrayAccess;
use \RuntimeException;
use JsonSerializable;

/**
 * Each instance of UploadGroup contains an array of UploadedFile objects instantiated with $_FILES super-global
 * primitives that correspond to single uploaded files. An instance of UploadGroup therefore represents all files
 * uploaded under a POST name
 */

class UploadGroup implements Iterator,ArrayAccess,JsonSerializable
{
    protected $UploadedFiles = [];//instances of \Upload\UploadedFile
    protected $position = 0;
    //keys associated with each file in $_FILES
    protected $fileKeys = [
        'name',
        'type',
        'tmp_name',
        'error',
        'size'
    ];

    /**
     * @param array $filesAttrGroup multi-array of name,type,size,tmp_name,error arrays from $_FILES
     */
    public function __construct(array $filesAttrGroup){
        if( $this->fileKeys != array_intersect($this->fileKeys, array_keys($filesAttrGroup)) ){
            throw new RuntimeException('constructor argument array is malformed with keys '.var_export(array_keys($filesAttrGroup), true));
        }
        if(is_array($filesAttrGroup['name'])){
            $fileCount = count($filesAttrGroup['name']);
            for($i=0; $i<$fileCount; $i++){
                $fileArray = $this->fileKeys;
                foreach($this->fileKeys as $key){
                    $fileArray[$key] = $filesAttrGroup[$key][$i];
                }
                $this->UploadedFiles[$i] = new UploadedFile($fileArray);
            }//\for
        }else{
            $this->UploadedFiles[] = new UploadedFile($filesAttrGroup);
        }
    }//\

    public function count(){
        return count($this->UploadedFiles);
    }//\
//-----------------------------------------ArrayAccess methods->
    public function offsetExists($offset){
        return isset($this->UploadedFiles[$offset]);
    }//\

    public function offsetGet($offset){
        return isset($this->UploadedFiles[$offset])? $this->UploadedFiles[$offset] : null;
    }//\

    public function offsetSet($offset, $value){
        if( is_null($offset) ){
            $this->UploadedFiles[] = $value;
        }
        else{
            $this->UploadedFiles[$offset] = $value;
        }
    }//\

    public function offsetUnset($offset){
        unset($this->UploadedFiles[$offset]);
    }//\


//-----------------------------------------Iterator methods->
    public function current(){
        return $this->UploadedFiles[$this->position];
    }//\
    public function key(){
        return $this->position;
    }//\
    public function next(){
        ++$this->position;
    }//\
    public function rewind(){
        $this->position = 0;
    }//\
    public function valid(){
        return isset($this->UploadedFiles[$this->position]);
    }//\
//----------------------------------------------------------JsonSerializable->
    public function jsonSerialize(){
        return $this->UploadedFiles;
    }
}////\
