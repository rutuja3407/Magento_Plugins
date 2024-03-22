<?php


namespace MiniOrange\SP\Helper;

class MoCurl extends \Magento\Framework\HTTP\Adapter\Curl
{
    protected $_header;
    protected $_body;
    public function __construct()
    {
        $this->_config["\x76\145\162\151\x66\171\x70\145\145\x72"] = false;
        $this->_config["\166\x65\162\x69\146\x79\150\157\x73\164"] = false;
        $this->_config["\x68\x65\x61\x64\x65\x72"] = false;
    }
}
