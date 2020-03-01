<?php
namespace App\RpcService;

class Rpcstatus
{
    // Informational 1xx
    const CODE_PARAM_ERROR = 100;
    
    private static $phrases = [
        100 => 'Error parameter type',
    ];
    
    public static function getReasonPhrase($statusCode):?string
    {
        if(isset(self::$phrases[$statusCode])){
            return self::$phrases[$statusCode];
        }else{
            return null;
        }
    }
}