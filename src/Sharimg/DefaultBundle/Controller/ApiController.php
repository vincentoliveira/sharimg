<?php

namespace Sharimg\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Base API Controller
 */
class ApiController extends Controller
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
     * Translate $msg
     * @param string $msg
     * @param array $params
     * @return string translation
     */
    public function trans($msg, $params = array())
    {
        return $this->get('translator')->trans($msg);
    }
    
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
