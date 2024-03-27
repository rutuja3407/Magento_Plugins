<?php


namespace MiniOrange\SP\Helper;

class MoCurl extends \Magento\Framework\HTTP\Adapter\Curl
{
    protected $_header;
    protected $_body;
    public function __construct()
    {
        $this->_config["\x76\145\x72\x69\x66\x79\160\145\x65\162"] = false;
        $this->_config["\166\x65\162\x69\x66\171\150\x6f\163\x74"] = false;
        $this->_config["\x68\145\x61\x64\145\162"] = false;
    }
}
