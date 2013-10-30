<?php

namespace Sharimg\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sharimg\DefaultBundle\Controller\BaseController;

/**
 * Base API Controller
 */
class ApiController extends BaseController
{
    const ERROR_OK          = 0;
    const ERROR_BAD_ARG     = 1;
    const ERROR_INTERNAL    = 127;
    
    protected $errorMsg = array(
        self::ERROR_OK => 'error.ok',
        self::ERROR_BAD_ARG => 'error.bad_arg',
        self::ERROR_INTERNAL => 'error.internal',
    );
    
    /**
     * Return error array (err_no, err_msg)
     * @param int $errNo
     * @param array $params
     * @return array
     */
    public function error($errNo, $params = array())
    {
        $result = array('err_no' => $errNo);
        
        if (array_key_exists($errNo, $this->errorMsg)) {
            $result['err_msg'] = $this->trans($this->errorMsg[$errNo], $params);
        }
        
        return array('error' => $result);
    }
}
