<?php

namespace Sharimg\DefaultBundle\Controller;

use Sharimg\DefaultBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Base API Controller
 */
class ApiController extends BaseController
{
    const ERROR_OK                  = 0;
    const ERROR_BAD_ARG             = 1;
    const ERROR_INVALID_ARGS        = 10;
    const ERROR_MISSING_PARAMS      = 2;
    const ERROR_UNIQUE_PARAMS       = 3;
    const ERROR_EMAIL_FORMAT        = 4;
    const ERROR_INTERNAL            = 127;
    
    protected $errorMsg = array(
        self::ERROR_OK => 'error.ok',
        self::ERROR_BAD_ARG => 'error.bad_arg',
        self::ERROR_INVALID_ARGS => 'error.invalid_args',
        self::ERROR_MISSING_PARAMS => 'error.missing_params',
        self::ERROR_UNIQUE_PARAMS => 'error.unique_params',
        self::ERROR_EMAIL_FORMAT => 'invalid_email_format',
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
        
        return new JsonResponse(array('error' => $result));
    }
    
    /**
     * Return error array (err_no, err_msg)
     * @param int $errNo
     * @param array $params
     * @return array
     */
    public function error2($error)
    {
        foreach ($error as $errNo => $args) {
            $result = array('err_no' => $errNo);

            if (array_key_exists($errNo, $this->errorMsg)) {
                $result['err_msg'] = $this->trans($this->errorMsg[$errNo], $args);
            }
            
            return new JsonResponse(array('error' => $result));
        }
    }
}
