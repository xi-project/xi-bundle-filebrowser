<?php

namespace Xi\Bundle\FilebrowserBundle\Service;

use Xi\Filelib\FileLibrary,      
    Symfony\Component\HttpFoundation\File\UploadedFile,
    Xi\Bundle\FilebrowserBundle\Form\UploadType,
    Symfony\Component\Form\FormFactory,
    Xi\Bundle\FilebrowserBundle\Component\Spl\CallbackFilterIterator as XiCallbackFilterIterator,
    \ArrayIterator;
    
class FileBrowserService 
{

    /**
     * @var FormFactory 
     */
    protected $formFactory;
    
    /**
     * @var FileLibrary
     */
    protected $filelib;
    
    /**
     * @var array
     */
    protected $config;  
    /**
     * @param FileLibrary   $filelib
     * @param FormFactory   $factory
     */
    public function __construct(FileLibrary $filelib, FormFactory $formFactory, $config)
    {
        $this->filelib     = $filelib;
        $this->formFactory = $formFactory;
        $this->config      = $config;
    }
     
    public function uploadAttachment(UploadedFile $uploadedFile = null, $profile = 'default')
    {      
        if($uploadedFile == null) {
            return null;
        }
        
        $folder = $this->filelib->getFolderOperator()->createByUrl($this->config['folder']);         
        
        // Prepare file upload 
        $upload = $this->filelib->getFileOperator()->prepareUpload($uploadedFile->getRealPath());
        
        // Override file name with info from client
        $upload->setOverrideFilename($uploadedFile->getClientOriginalName());
        
        // Upload file
        $file = $this->filelib->getFileOperator()->upload($upload, $folder, $profile);
      
        return $file;
    }
    
    /**
     * @return array
     */
    public function getFiles()
    {
        $folderOperator = $this->filelib->getFolderOperator();
        $folder = $folderOperator->createByUrl($this->config['folder']);
        return $folderOperator->findFiles($folder);
    }
    
    /**
     * @param array $files
     * @param array $type
     * @return CallbackFilterIterator 
     */
    public function filterByType($files, array $type)
    {
        $filesObject = new \ArrayObject($files);
        $fileOperator = $this->filelib->getFileOperator();   

        $filteredFiles = array();
        if (version_compare(phpversion(),'5.4.0') >= 0) {
            $filteredFiles = new \CallbackFilterIterator($filesObject->getIterator(), function ($current, $key) use ($type, $fileOperator) {
                return (in_array($fileOperator->getType($current),$type));
            }); 
        } else {             
            $filteredFiles = new XiCallbackFilterIterator($filesObject->getIterator(), function ($current, $key) use ($type, $fileOperator) {
                return (in_array($fileOperator->getType($current),$type));
            });                   
        }
        return $filteredFiles;
    }
    
    
    /**
     * @return Symfony\Component\Form\Form
     */
    public function getUploadForm()
    {     
        $form  = $this->formFactory->create(new UploadType());
        return $form;
    }
  
    
}