<?php
namespace app\weiwin\common;

class paramters
{
    //获取商家代号
    public function getBusiness()
    {
        if(session('business')){
            $bsn=session('business');
        }  else {
            $bsn='1000000';
        }
        return $bsn;
    }
}

