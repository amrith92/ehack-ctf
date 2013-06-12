<?php

namespace CTF\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CTF\SecurityBundle\Exception\AccessDeniedException;

class UploadController extends Controller {

    public function uploadAction(Request $request) {
        if (false === $this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $editId = $request->get('editId');
        if (!preg_match('/^\d+$/', $editId)) {
            throw new \Exception("Bad edit id");
        }

        $this->get('punk_ave.file_uploader')->handleFileUpload(array('folder' => 'tmp/attachments/' . $editId));
    }

}
