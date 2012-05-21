<?php

namespace Xi\FileBrowserBundle\Tests\Service;

use PHPUnit_Framework_TestCase,
    Xi\FileBrowserBundle\Service\FileBrowserService,
    Xi\Doctrine\Fixtures\FieldDef,
    Symfony\Component\HttpFoundation\File\UploadedFile,
    Xi\Filelib\File\FileOperator,
    Xi\Filelib\File\FileItem,
    Xi\Filelib\FileLibrary,
    Xi\Filelib\File\Upload\FileUpload,
    Xi\Filelib\Folder\FolderOperator;

/**
 * @group service
 * @group filebrowser
 */
class FileBrowserServiceTest extends PHPUnit_Framework_Testcase
{
    /**
     * @var FileBrowserService
     */
    protected $service;


    protected $filelib;
    
    protected $folderOperator;
    
    protected $fileOperator;
    
    protected $uploadedFile;
    
    
    public function setUp()
    {
        parent::setUp();

        $this->folderOperator = $this->getMockBuilder('Xi\Filelib\Folder\DefaultFolderOperator')->disableOriginalConstructor()->getMock();
        $this->fileOperator = $this->getMockBuilder('Xi\Filelib\File\DefaultFileOperator')->disableOriginalConstructor()->getMock();
        $this->fileUpload= $this->getMockBuilder('Xi\Filelib\File\Upload\FileUpload')->disableOriginalConstructor()->getMock();

        $this->filelib = $this->getMockBuilder('Xi\Filelib\FileLibrary')->disableOriginalConstructor()->getMock();
        $this->filelib->expects($this->any())->method('getFolderOperator')->will($this->returnValue($this->folderOperator));
        $this->filelib->expects($this->any())->method('getFileOperator')->will($this->returnValue($this->fileOperator));

        $this->formFactory =  $this->getMockBuilder('Symfony\Component\Form\FormFactory')->disableOriginalConstructor()->getMock();
        $this->service = new FileBrowserService(
            $this->filelib,
            $this->formFactory,
            array('folder' => 'puuppa')
        );
        
        $this->folder =  $this->getMockBuilder('Xi\Filelib\Folder\FolderItem')->disableOriginalConstructor()->getMock();
        $this->uploadedFile = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
                                       ->disableOriginalConstructor()->getMock();         
    }
    
    /**
     * @test
     */
    public function uploadAttachment()
    {
        $this->folderOperator->expects($this->once())->method('createByUrl')->with('puuppa')->will($this->returnValue($this->folder));
   
        $this->fileOperator->expects($this->once())->method('prepareUpload')->will($this->returnValue($this->fileUpload));        
        $this->file = $this->getMockBuilder('Xi\Filelib\File\FileItem')->disableOriginalConstructor()->getMock();

        $this->fileOperator->expects($this->once())->method('upload')->with(
                $this->anything(),
                $this->isInstanceOf('Xi\Filelib\Folder\Folder')
                )->will($this->returnValue($this->file));
        
        
        $file = $this->service->uploadAttachment($this->uploadedFile);
        $this->assertInstanceOf('Xi\Filelib\File\FileItem', $file);
    }
   
    /**
     * @test
     */
    public function getUploadForm()
    {   
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $this->formFactory->expects($this->once())->method('create')->will($this->returnValue($form));     
        $returnedForm = $this->service->getUploadForm();   
        $this->assertInstanceOf('Symfony\Component\Form\Form', $returnedForm);
    }
    
    /**
     * @test
     */
    public function getFiles()
    {
        $this->folderOperator->expects($this->once())->method('createByUrl')->with('puuppa')->will($this->returnValue($this->folder));
        $this->folderOperator->expects($this->once())->method('findFiles')->with($this->folder)->will($this->returnValue(array('file', 'file2')));
        $files = $this->service->getFiles();
        $this->assertCount(2, $files);
    }
 
    /**
     * @test
     */
    public function filterByType()
    {
       $this->fileOperator->expects($this->any())->method('getType')->will($this->returnCallback(function($file) { 
           $luss = explode('/', $file->getMimeType());
           return $luss[0];
       }));  
       
       $files = array();
       $files[] = $this->createFileItem('image/jpeg');
       $files[] = $this->createFileItem('application/luss');
       $files[] = $this->createFileItem('image/png');
       $files[] = $this->createFileItem('media/xooxoo');
       $files[] = $this->createFileItem('application/tussi');
       $files[] = $this->createFileItem('image/jpeg');
       $files[] = $this->createFileItem('text/plain');
 
       $imagefiles = $this->service->filterByType($files, array('image'));    
       $this->assertCount(3, $imagefiles);
       
       $applicationfiles = $this->service->filterByType($files, array('application'));    
       $this->assertCount(2, $applicationfiles);       

    }
    
    private function createFileItem($mimetype)
    {
       return FileItem::create(array('mimetype' => $mimetype));
    }

    
}