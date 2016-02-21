<?php
namespace Upload;

/**
 * USE:
 *
 * $ug = new UploadGroup($array_from_uploaded_FILES_under_a_specific_upload_key);//e.g. $_FILES['foo'];
 * //get any single file by its offset
 * $ug[0]; $ug[1];
 * //iterable; vales are instances of UploadedFile
 * foreach($ug as $file){
 *    $file->name: //name of file
 * }
 */
class UploadGroupTest extends \PHPUnit_Framework_TestCase
{
    protected
        $singleFileGroup,
        $multiFileGroup = [];

    protected function setUp( ) {
        $this->multiFileGroup = [
            'name' => [
                'filename_1',
                'filename_2',
                'filename_3'
            ],
            'type'=> [
                'image/jpeg',
                'image/png',
                'text/html'
            ],
            'tmp_name'=>[
                tempnam(sys_get_temp_dir(), 'mock'),
                tempnam(sys_get_temp_dir(), 'mock'),
                tempnam(sys_get_temp_dir(), 'mock')
            ],
            'error'=>[
                UPLOAD_ERR_OK,
                UPLOAD_ERR_OK,
                UPLOAD_ERR_OK
            ],
            'size'=> [
                5312014,
                642395,
                3133
            ]
        ];
        $this->singleFileGroup = [
            'name' => $this->multiFileGroup['name'][0],
            'type' => $this->multiFileGroup['type'][0],
            'tmp_name' => $this->multiFileGroup['tmp_name'][0],
            'error' => $this->multiFileGroup['error'][0],
            'size' => $this->multiFileGroup['size'][0]
        ];
    }//\

    /**
    * @expectedException RuntimeException
    */
    public function testConstructBadArray(){
        new UploadGroup([]);
    }//\

    public function testConsructGoodSingleFile(){
        $ug = new UploadGroup($this->singleFileGroup);
        return $ug;
    }

    public function testConstructGoodMultiFile(){
        $ug = new UploadGroup($this->multiFileGroup);
        return $ug;
    }//\

    public function testArrayAccessMultiFile(){
        $ug = $this->testConstructGoodMultiFile();
        $this->assertInstanceOf('Upload\UploadedFile', $ug[1]);
        $this->assertEquals($this->multiFileGroup['name'][1], $ug[1]->name);
        $this->assertEquals($this->multiFileGroup['type'][2], $ug[2]->type);
    }//\

    public function testIterableMultiFile(){
        $ug = $this->testConstructGoodMultiFile();
        foreach($ug as $k=>$file){
            $this->assertEquals($this->multiFileGroup['name'][$k], $file->name);
            $this->assertEquals($this->multiFileGroup['type'][$k], $file->type);
            $this->assertEquals($this->multiFileGroup['size'][$k], $file->size);
            $this->assertEquals($this->multiFileGroup['tmp_name'][$k], $file->tmp_name);
            $this->assertEquals($this->multiFileGroup['error'][$k], $file->error);
        }//\foreach
    }//\

    public function testArrayAccessSingleFile(){
        $ug = $this->testConsructGoodSingleFile();
        $this->assertInstanceOf('Upload\UploadedFile', $ug[0]);
        $this->assertEquals($this->singleFileGroup['name'], $ug[0]->name);
        $this->assertEquals($this->singleFileGroup['type'], $ug[0]->type);
    }//\

    public function testIterableSingleFile(){
        $ug = $this->testConsructGoodSingleFile();
        foreach($ug as $k=>$file){
            $this->assertEquals($this->singleFileGroup['name'], $file->name);
            $this->assertEquals($this->singleFileGroup['type'], $file->type);
            $this->assertEquals($this->singleFileGroup['size'], $file->size);
            $this->assertEquals($this->singleFileGroup['tmp_name'], $file->tmp_name);
            $this->assertEquals($this->singleFileGroup['error'], $file->error);
        }//\foreach
    }//\

    protected function tearDown( ) {
        foreach($this->multiFileGroup['tmp_name'] as $f){
            unlink($f);
        }
    }//\

}////\
