<?php
namespace Upload;

use RuntimeException;
use JsonSerializable;

/**
 * An UploadedFile instance holds all information about an uploaded file.
 * It's instantiated by an UploadGroup instance
 *
 */

class UploadedFile implements JsonSerializable
{
    protected//$_FILE offsets
        $name,
        $type,
        $tmp_name,
        $error,
        $size;

    //use to check that all $_FILE offsets are present
    protected $fileKeys = [
        'name',
        'type',
        'tmp_name',
        'error',
        'size'
    ];

    public function __construct(array $file){
        if( array_intersect($this->fileKeys, array_keys($file)) != $this->fileKeys ){
            throw new RuntimeException('malfored $files array');
        }
        $this->name = $file['name'];
        $this->size = $file['size'];
        $this->type = $file['type'];
        $this->tmp_name = $file['tmp_name'];
        $this->error = $file['error'];

        $this->_errorCheck();
    }//\

    /**
     * get the contents of the file at tmp_name
     * @throws RuntimeException
     * @return mixed the return value of file_get_contents
     */
    public function getContents(){
        $contents = file_get_contents($this->tmp_name);
        if( false === $contents ){
            throw new RuntimeException('unable to get contents of uploaded file '.$this->name);
        }
        return $contents;
    }//\

    /**
     * use magic getter to get protected properties which correspond to $_FILE keys
     * @param   string $offset a $_FILE property
     * @return  mixed  string or int value of a $_FILE property
     */
    public function __get($offset){
        if ( in_array($offset, $this->fileKeys) ){
            return $this->{$offset};
        }
    }//\

    /**
     * turns upload errors from $_FILE error into instances of UploadFileException with descriptive messages
     * @throws UploadFileException
     */
    private function _errorCheck( ) {
        if( $this->error != UPLOAD_ERR_OK ){
            switch ($this->error) {
                case UPLOAD_ERR_INI_SIZE:
                    $errorMessage = 'The file '.$this->name.' exceeds the server maximum file size';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $errorMessage = 'The file '.$this->name.' exceeds the upload form maximum file size';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorMessage = 'The file '.$this->name.' was only partially uploaded';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMessage = 'The file '.$this->name.' failed to upload';
                    break;
                default:
                    $errorMessage = 'An unknown error occurred';
            }//\switch
            throw new UploadedFileException($errorMessage, $this->error);
        }
    }//\

//-------------------------------------------------------------JsonSerializable->
    public function jsonSerialize(){
        return [
            'name' => $this->name,
            'type' => $this->type,
            'tmp_name' => $this->tmp_name,
            'error' => $this->error,
            'size' => $this->size
        ];
    }

}//\
