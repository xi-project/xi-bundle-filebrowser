<?php

namespace Xi\FilebrowserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Xi\Filelib\FileLibrary;
use Xi\Filelib\Folder\DefaultFolderOperator;
use Xi\Filelib\File\DefaultFileOperator;
use Xi\FileBrowserBundle\Service\FileBrowserService;

class FilebrowserController extends Controller
{
  
    public function listAction($type = 'any')
    {   
        $this->get('session')->set('filebrowser_type', $type);
        $files = $this->getFileBrowserService()->getFiles();
                
        if($type !== 'any'){
            $files = $this->getFileBrowserService()->filterByType($files, array($type));
        }
        
        return $this->render('XiFilebrowserBundle:Filebrowser:list.html.twig', array(
            'files'        => $files,
            'uploadForm'   => $this->getFileBrowserService()->getUploadForm()->createView(),
            'fileoperator' => $this->getFileOperator()
        ));
    }
    
     /**
     * @param  Request $request 
     * @return UploadedFile|null
     */
    protected function getUploadedFileData()
    {
        $request = $this->getRequest();
        
        if(isset($request->files)) {
            $data = $request->files->get('xi_filebrowserbundle_uploadtype');            
            return isset($data['file']) ? $data['file'] : null;
        }
    }
    
    public function uploadAction()
    {
        $self                = $this;
        $request             = $this->getRequest();
              
        $uploadedFile = $this->getUploadedFileData();
        $this->getFileBrowserService()->uploadAttachment($uploadedFile, 'articleimage');
        
        return $this->redirect($this->generateUrl('xi_filelib_filebrowser_list', array('type' => $this->get('session')->get('filebrowser_type'))));
    }    
    
    /**
     * @return FileBrowserService
     */
    private function getFileBrowserService()
    {
        return $this->container->get('xi_filebrowser.service.filebrowser');
    }
    
    /**
     * @return DefaultFileOperator
     */
    private function getFileOperator()
    {
        return $this->get('filelib')->getFileOperator();
    }
    /**
     * @return DefaultFolderOperator
     */
    private function getFolderOperator()
    {
        return $this->get('filelib')->getFolderOperator();
    }
}
