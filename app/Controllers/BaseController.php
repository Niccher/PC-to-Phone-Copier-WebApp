<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
	protected $helpers = ['text', 'form', 'filesystem','cookie'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
	    $this->session = \Config\Services::session();
    }

    protected function getSidebarData($title = '')
    {
        $sess_id = $this->session->get('sess_id');
        $mod_upload = new \App\Models\ModUpload();
        $mod_text = new \App\Models\ModText();

        $files = $mod_upload->file_get_uploaded_files($sess_id);
        $texts = $mod_text->text_get_uploaded_texts($sess_id);
        $deleted_files = $mod_upload->get_deleted_files($sess_id);
        $deleted_texts = $mod_text->get_deleted_texts($sess_id);
        
        $recent_files = $mod_upload->file_get_uploaded_files($sess_id, 10);
        $recent_texts = $mod_text->text_get_uploaded_texts($sess_id, 10);

        return [
            'files_count'  => count($files),
            'texts_count'  => count($texts),
            'trash_count'  => count($deleted_files) + count($deleted_texts),
            'recent_count' => count($recent_files) + count($recent_texts),
            'title'        => $title
        ];
    }
}
