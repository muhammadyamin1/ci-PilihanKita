<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class UploadController extends Controller
{
    public function showCalon($filename)
    {
        // Ambil file dari writable/uploads/calon/
        $path = WRITEPATH . 'uploads/calon/' . $filename;

        // Cek apakah file ada
        if (!file_exists($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($path))
            ->setBody(file_get_contents($path));
    }
}