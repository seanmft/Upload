<?php
namespace Upload\Tests;
use Upload\Uploads;

/**
 * USE:
 * $u = new Uploads(); //no arguments, constructor looks for $_FILES super-global
 * //get total number of files uploaded
 * $u->count();
 * //array access for upload namespaces
 * $u['photo-upload'];//UploadGroup instance
 * //access files
 * foreach($u['photo-upload'] as $file){
 *     $file->name;//name file was uploaded with
 *     $file->tmp_name;//temp file path
 *     $file->size;//size of file in bytes
 *     $file->type;//mime type
 * }
 */

class UploadsTest extends \PHPUnit_Framework_TestCase
{
    public function pseudoCode(){
        //the constructor can access the global $_FILES array
        $u = new Uploads();
        //gives you the number of files uploaded
        $u->count();
        //gives the total size of upload in bytes
        $u->size();
        //ArrayAccess for upload namespaces
        $pu = $u['photo-upload'];
    }//\

    protected function setUp( ){
        $this->mockNames = [
            'filename_1',
            'filename_2',
            'filename_3'
        ];
        $this->mockTypes = [
            'image/jpeg',
            'image/png',
            'text/html'
        ];
        $this->mockSizes = [
            5312014,
            642395,
            3133
        ];
        $this->mockTmp_names = [
            tempnam(sys_get_temp_dir(), 'mock'),
            tempnam(sys_get_temp_dir(), 'mock'),
            tempnam(sys_get_temp_dir(), 'mock')
        ];
        $this->mockErrors = [
            UPLOAD_ERR_OK,
            UPLOAD_ERR_OK,
            UPLOAD_ERR_OK
        ];
        $this->mock_FILES = [
            'namespace-1' => [
                'name'=> $this->mockNames,
                'type'=> $this->mockTypes,
                'size'=> $this->mockSizes,
                'tmp_name'=> $this->mockTmp_names,
                'error'=> $this->mockErrors
            ]
        ];
        $this->mock_FILES['namespace-2'] = $this->mock_FILES['namespace-1'];
    }//\

    protected function _getMockedUploadsInstance(){
        $_FILES = $this->mock_FILES;
        return new Uploads();
    }//\

    /**
    * @expectedException RuntimeException
    */
    public function testFailEmptyFilesGlobal(){
        $this->setExpectedException(
            '\RuntimeException',
            'The $_FILES super-global array is empty; nothing to do'
        );
        $u = new Uploads();
    }//\

    public function testConstructMock_FILESGlobal(){
        $this->_getMockedUploadsInstance();
    }//\

    public function testCount(){
        $u = $this->_getMockedUploadsInstance();
        $actualCount = $u->count();
        $expectedCount = count($this->mockNames) * count($this->mock_FILES);
        $this->assertEquals($expectedCount, $actualCount);
    }//\

    public function testArrayAccess(){
        $u = $this->_getMockedUploadsInstance();
        $this->assertInstanceOf('\Upload\UploadGroup', $u['namespace-1']);
    }//\

    protected function tearDown(){
        //apparently these aren't deleted automatically by php
        foreach($this->mockTmp_names as $tmp_name){
            unlink($tmp_name);
        }
    }
}////\
