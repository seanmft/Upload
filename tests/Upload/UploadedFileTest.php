<?php
namespace Upload\Tests;
use Upload\UploadedFile;
/**
 * USE:
 * $uf = new UploadedFile($uploaded_file_array_for_a_single_file); // ['name', 'type', 'size', 'tmp_name', 'error']
 * //magic get after instantiation
 * $uf->name; // the name of the file when uploaded
 * $uf->type; // the mime type e.g. 'image/jpeg'
 * //etc.
 */
class UploadedFileTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp( ){
        $this->_files = [
            'name'=>'somefile.gif',
            'type'=>'image/gif',
            'tmp_name'=>'xzfuc3oc',
            'error'=>UPLOAD_ERR_OK,
            'size'=>7653120
        ];
        $this->uf = new UploadedFile($this->_files);
    }//\

    /**
    * @expectedException PHPUnit_Framework_Error
    */
    public function testMissingArg(){
        $uf = new UploadedFile();
    }//\

    /**
    * @expectedException RuntimeException
    */
    public function testImproperArrayArg( ){
        new UploadedFile(['name'=>'foo']);
    }//\

    /**
    * @expectedException \Upload\UploadedFileException
    */
    public function testExceptionOnFileError( ){
        $_files = $this->_files;
        $_files['error'] = UPLOAD_ERR_INI_SIZE;
        $this->expectExceptionMessage(
            'The file '.$this->_files['name'].' exceeds the server maximum file size'
        );
        new UploadedFile($_files);
    }//\

    public function testMagicGetter( ){
        $uf = clone $this->uf;

        $actualName = $uf->name;
        $expectedName = $this->_files['name'];
        $this->assertEquals($expectedName, $actualName);

        $actualType = $uf->type;
        $expectedType = $this->_files['type'];
        $this->assertEquals($expectedType, $actualType);

        //$this->markTestIncomplete('add other $_FILES offsets');
    }//\

}////\
