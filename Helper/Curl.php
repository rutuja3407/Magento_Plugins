<?php


namespace MiniOrange\SP\Helper;

class Curl
{
    public static function create_customer($EK, $tz, $XM, $zw = '', $hI = '', $fi = '')
    {
        $At = SPConstants::HOSTNAME . "\x2f\x6d\157\x61\163\57\x72\x65\x73\164\x2f\143\165\163\164\157\155\x65\162\x2f\141\x64\144";
        $rY = SPConstants::DEFAULT_CUSTOMER_KEY;
        $UE = SPConstants::DEFAULT_API_KEY;
        $qy = array("\143\157\155\x70\141\x6e\171\116\141\x6d\x65" => $tz, "\x61\162\145\x61\x4f\x66\x49\x6e\164\145\162\145\x73\x74" => SPConstants::AREA_OF_INTEREST, "\146\x69\162\x73\164\156\x61\x6d\x65" => $hI, "\x6c\x61\x73\164\156\x61\155\x65" => $fi, "\145\x6d\141\x69\154" => $EK, "\x70\150\x6f\156\145" => $zw, "\x70\x61\x73\163\x77\x6f\x72\144" => $XM);
        $cb = self::createAuthHeader($rY, $UE);
        $J_ = self::callAPI($At, $qy, $cb);
        return $J_;
    }
    private static function createAuthHeader($rY, $UE)
    {
        $SC = round(microtime(true) * 1000);
        $SC = number_format($SC, 0, '', '');
        $Wx = $rY . $SC . $UE;
        $cb = hash("\163\150\x61\x35\61\x32", $Wx);
        $db = array("\103\x6f\x6e\x74\x65\x6e\x74\55\124\171\160\x65\72\x20\x61\x70\160\154\151\x63\141\x74\151\157\156\x2f\152\163\x6f\156", "\103\165\163\164\157\x6d\x65\x72\55\113\x65\x79\x3a\x20{$rY}", "\124\x69\x6d\x65\x73\164\141\x6d\160\x3a\40{$SC}", "\x41\165\164\x68\157\162\x69\x7a\141\164\151\157\x6e\x3a\x20{$cb}");
        return $db;
    }
    private static function callAPI($At, $Xw = array(), $Og = array("\103\x6f\x6e\164\x65\156\x74\55\124\x79\x70\145\72\40\141\x70\x70\154\151\x63\x61\164\151\x6f\156\57\x6a\x73\x6f\156"))
    {
        $aO = new MoCurl();
        $Es = array("\103\125\x52\x4c\x4f\x50\x54\137\x46\x4f\x4c\x4c\117\x57\114\x4f\x43\101\124\111\x4f\116" => true, "\103\x55\122\x4c\117\120\x54\137\105\116\x43\x4f\104\111\x4e\107" => '', "\103\x55\x52\x4c\x4f\120\x54\137\122\105\x54\125\122\x4e\124\122\101\116\x53\x46\105\x52" => true, "\103\125\x52\114\x4f\x50\x54\x5f\101\125\124\117\x52\105\106\105\x52\105\122" => true, "\103\x55\x52\x4c\x4f\120\x54\137\124\x49\115\x45\x4f\125\124" => 0, "\103\125\x52\x4c\x4f\120\x54\137\115\x41\130\x52\x45\104\111\122\x53" => 10);
        $vh = !empty($Xw) ? "\x50\x4f\x53\124" : "\107\x45\124";
        $aO->setConfig($Es);
        $aO->write($vh, $At, "\x31\x2e\61", $Og, !empty($Xw) ? json_encode($Xw) : '');
        $Qz = $aO->read();
        $aO->close();
        return $Qz;
    }
    public static function get_customer_key($EK, $XM)
    {
        $At = SPConstants::HOSTNAME . "\x2f\x6d\x6f\141\163\57\162\x65\163\x74\57\x63\x75\x73\x74\157\x6d\145\x72\x2f\x6b\x65\171";
        $rY = SPConstants::DEFAULT_CUSTOMER_KEY;
        $UE = SPConstants::DEFAULT_API_KEY;
        $qy = array("\145\x6d\141\x69\154" => $EK, "\160\141\x73\163\x77\x6f\162\144" => $XM);
        $cb = self::createAuthHeader($rY, $UE);
        $J_ = self::callAPI($At, $qy, $cb);
        return $J_;
    }
    public static function check_customer($EK)
    {
        $At = SPConstants::HOSTNAME . "\x2f\155\157\141\163\57\162\x65\163\164\57\143\165\x73\164\x6f\155\x65\x72\57\143\x68\145\x63\x6b\x2d\x69\146\55\x65\x78\151\163\x74\x73";
        $rY = SPConstants::DEFAULT_CUSTOMER_KEY;
        $UE = SPConstants::DEFAULT_API_KEY;
        $qy = array("\x65\155\141\151\x6c" => $EK);
        $cb = self::createAuthHeader($rY, $UE);
        $J_ = self::callAPI($At, $qy, $cb);
        return $J_;
    }
    public static function mo_send_otp_token($SG, $EK = '', $zw = '')
    {
        $At = SPConstants::HOSTNAME . "\x2f\155\157\x61\x73\57\x61\160\x69\57\141\165\164\150\x2f\x63\x68\x61\154\154\x65\156\x67\145";
        $rY = SPConstants::DEFAULT_CUSTOMER_KEY;
        $UE = SPConstants::DEFAULT_API_KEY;
        $qy = array("\x63\x75\163\x74\x6f\x6d\145\x72\113\x65\171" => $rY, "\145\155\141\x69\154" => $EK, "\160\150\x6f\156\x65" => $zw, "\141\165\164\x68\x54\171\x70\x65" => $SG, "\x74\x72\141\156\163\141\x63\x74\x69\157\x6e\x4e\x61\155\x65" => SPConstants::AREA_OF_INTEREST);
        $cb = self::createAuthHeader($rY, $UE);
        $J_ = self::callAPI($At, $qy, $cb);
        return $J_;
    }
    public static function validate_otp_token($M5, $aE)
    {
        $At = SPConstants::HOSTNAME . "\57\155\x6f\x61\163\57\141\x70\x69\57\141\x75\x74\x68\x2f\x76\x61\154\151\x64\141\x74\145";
        $rY = SPConstants::DEFAULT_CUSTOMER_KEY;
        $UE = SPConstants::DEFAULT_API_KEY;
        $qy = array("\x74\170\x49\144" => $M5, "\x74\157\x6b\x65\156" => $aE);
        $cb = self::createAuthHeader($rY, $UE);
        $J_ = self::callAPI($At, $qy, $cb);
        return $J_;
    }
    public static function submit_contact_us($G7, $UT, $NB, $a9)
    {
        $At = SPConstants::HOSTNAME . "\x2f\155\x6f\141\x73\x2f\162\145\x73\x74\x2f\143\x75\163\x74\x6f\x6d\145\x72\57\x63\157\x6e\x74\x61\x63\x74\x2d\165\163";
        $NB = "\x5b" . SPConstants::AREA_OF_INTEREST . "\x5d\x3a\40" . $NB;
        $rY = SPConstants::DEFAULT_CUSTOMER_KEY;
        $UE = SPConstants::DEFAULT_API_KEY;
        $qy = array("\x63\157\155\160\x61\156\x79" => $a9, "\145\x6d\141\x69\x6c" => $G7, "\x70\150\x6f\156\x65" => $UT, "\161\x75\x65\x72\x79" => $NB, "\143\143\105\x6d\141\151\154" => "\155\x61\x67\x65\156\x74\157\x73\165\x70\x70\x6f\162\164\x40\x78\x65\x63\x75\x72\x69\x66\171\x2e\143\157\155");
        $cb = self::createAuthHeader($rY, $UE);
        $J_ = self::callAPI($At, $qy, $cb);
        return TRUE;
    }
    public static function forgot_password($EK, $rY, $UE)
    {
        $At = SPConstants::HOSTNAME . "\57\155\x6f\x61\x73\x2f\162\145\x73\x74\x2f\x63\165\x73\x74\x6f\155\x65\162\57\x70\x61\163\163\x77\157\x72\x64\55\x72\x65\163\x65\x74";
        $qy = array("\x65\155\141\x69\154" => $EK);
        $cb = self::createAuthHeader($rY, $UE);
        $J_ = self::callAPI($At, $qy, $cb);
        return $J_;
    }
    public static function check_customer_ln($rY, $UE)
    {
        $At = SPConstants::HOSTNAME . "\x2f\x6d\x6f\x61\163\57\162\x65\x73\x74\57\143\x75\163\164\157\x6d\x65\162\57\x6c\151\x63\145\x6e\x73\x65";
        $qy = array("\x63\165\163\x74\x6f\155\x65\x72\111\x64" => $rY, "\141\160\160\154\151\143\141\164\151\x6f\156\x4e\x61\x6d\x65" => SPConstants::APPLICATION_NAME, "\154\x69\x63\x65\156\x73\145\x54\171\160\x65" => !MoUtility::micr() ? "\x44\105\x4d\x4f" : "\x50\x52\x45\115\111\x55\x4d");
        $cb = self::createAuthHeader($rY, $UE);
        $J_ = self::callAPI($At, $qy, $cb);
        return $J_;
    }
    public static function ccl($rY, $UE)
    {
        $At = SPConstants::HOSTNAME . "\x2f\x6d\x6f\x61\163\x2f\162\145\163\164\57\143\x75\x73\x74\157\155\145\x72\x2f\154\151\143\145\x6e\x73\x65";
        $qy = array("\x63\165\x73\164\157\155\x65\x72\x49\144" => $rY, "\141\160\x70\x6c\151\143\x61\x74\151\x6f\156\116\141\155\x65" => SPConstants::LICENSE_PLAN);
        $cb = self::createAuthHeader($rY, $UE);
        $J_ = self::callAPI($At, $qy, $cb);
        return $J_;
    }
    public static function vml($rY, $UE, $wI, $wT)
    {
        $At = SPConstants::HOSTNAME . "\x2f\x6d\157\x61\163\x2f\141\160\x69\57\x62\141\143\x6b\x75\x70\x63\x6f\x64\145\x2f\x76\145\162\151\146\x79";
        $qy = array("\143\x6f\144\145" => $wI, "\x63\165\163\164\x6f\x6d\145\x72\113\x65\x79" => $rY, "\x61\144\144\x69\164\x69\157\x6e\x61\x6c\x46\151\145\154\x64\163" => array("\x66\x69\x65\154\x64\x31" => $wT));
        $cb = self::createAuthHeader($rY, $UE);
        $J_ = self::callAPI($At, $qy, $cb);
        return $J_;
    }
    public static function mius($rY, $UE, $wI)
    {
        $At = SPConstants::HOSTNAME . "\57\x6d\x6f\141\163\57\x61\160\x69\57\142\x61\x63\x6b\x75\x70\143\x6f\x64\145\57\165\160\x64\141\x74\x65\163\164\141\164\x75\163";
        $qy = array("\x63\x6f\144\x65" => $wI, "\x63\x75\163\164\157\155\x65\162\x4b\x65\x79" => $rY);
        $cb = self::createAuthHeader($rY, $UE);
        $J_ = self::callAPI($At, $qy, $cb);
        return $J_;
    }
    public static function update_status($rY, $UE, $wI, $wT)
    {
        $At = SPConstants::HOSTNAME . "\x2f\155\157\141\x73\x2f\141\x70\151\57\x62\x61\x63\x6b\x75\x70\143\x6f\x64\145\57\x75\160\x64\141\x74\145\x73\164\x61\164\x75\x73";
        $qy = array("\x63\157\144\x65" => $wI, "\143\x75\x73\164\157\x6d\x65\162\x4b\x65\x79" => $rY, "\x61\x64\x64\151\164\151\157\156\141\154\106\x69\145\x6c\144\x73" => array("\146\151\x65\154\144\61" => $wT));
        $cb = self::createAuthHeader($rY, $UE);
        $J_ = self::callAPI($At, $qy, $cb);
        return $J_;
    }
    public static function submit_to_magento_team($G7, $gW, $Qz)
    {
        $Qz = implode("\54\x20", $Qz);
        $At = SPConstants::HOSTNAME . "\x2f\x6d\x6f\x61\x73\57\141\x70\x69\57\x6e\x6f\164\x69\146\x79\57\163\x65\156\144";
        $rY = SPConstants::DEFAULT_CUSTOMER_KEY;
        $UE = SPConstants::DEFAULT_API_KEY;
        $TA = array("\x63\x75\163\x74\157\x6d\x65\x72\113\145\171" => $rY, "\163\145\156\x64\x45\155\141\151\x6c" => true, "\145\155\x61\x69\x6c" => array("\143\x75\x73\164\157\155\145\162\113\145\x79" => $rY, "\x66\x72\x6f\155\x45\x6d\x61\151\154" => "\x6e\151\x74\145\163\x68\x2e\160\x61\155\x6e\x61\156\x69\100\170\x65\143\x75\162\151\146\x79\x2e\143\x6f\155", "\x62\x63\143\105\155\141\x69\154" => "\162\165\164\165\152\141\x2e\x73\x6f\x6e\x61\x77\x61\x6e\145\x40\x78\x65\x63\x75\x72\x69\x66\171\56\143\157\x6d", "\x66\x72\157\x6d\x4e\x61\155\145" => "\155\151\x6e\151\117\162\141\x6e\x67\145", "\x74\x6f\x45\155\141\x69\154" => "\x6e\151\x74\x65\x73\150\x2e\160\141\155\156\141\156\151\x40\x78\x65\x63\165\x72\x69\146\171\56\143\x6f\155", "\164\157\116\x61\155\x65" => "\x4e\x69\164\x65\163\150", "\163\165\x62\x6a\145\143\x74" => "\115\141\147\145\156\164\157\x20\62\x2e\x30\40\123\x41\115\x4c\40\123\120\x20\124\162\x69\x61\x6c\40\x50\154\165\x67\x69\156\x20{$gW}\x20\x3a\x20{$G7}", "\143\157\156\x74\145\156\164" => $Qz));
        $xW = array("\143\x75\163\164\157\155\x65\x72\113\145\171" => $rY, "\163\145\156\144\105\155\141\x69\154" => true, "\145\155\x61\151\154" => array("\x63\x75\x73\x74\157\155\x65\162\113\145\171" => $rY, "\x66\x72\x6f\155\105\155\141\151\154" => "\162\x75\163\x68\151\x6b\x65\x73\150\x2e\156\x69\153\x61\155\100\x78\145\143\x75\x72\151\x66\x79\x2e\143\x6f\155", "\x62\x63\x63\x45\x6d\x61\151\x6c" => "\x72\141\152\x40\170\x65\143\x75\162\151\146\171\x2e\143\x6f\x6d", "\x66\162\x6f\x6d\116\x61\155\x65" => "\155\x69\156\x69\x4f\162\x61\156\x67\145", "\x74\157\105\x6d\141\x69\154" => "\162\165\163\150\x69\x6b\145\163\150\x2e\x6e\151\x6b\141\155\100\170\x65\x63\165\162\151\x66\x79\x2e\x63\x6f\x6d", "\x74\x6f\116\x61\155\x65" => "\x52\x75\x73\x68\151\x6b\x65\x73\x68", "\x73\165\x62\152\x65\x63\x74" => "\115\141\x67\x65\156\x74\x6f\x20\x32\56\x30\x20\x53\101\x4d\114\40\x53\x50\x20\x54\x72\151\x61\x6c\x20\120\x6c\165\x67\x69\x6e\40{$gW}\40\x3a\40{$G7}", "\143\157\x6e\164\145\x6e\164" => $Qz));
        $sF = json_encode($TA);
        $vi = json_encode($xW);
        $cb = self::createAuthHeader($rY, $UE);
        $zs = self::callAPI($At, $TA, $cb);
        $i0 = self::callAPI($At, $xW, $cb);
        return true;
    }
    public static function notify($rY, $UE, $XV, $Qz, $fH)
    {
        $At = SPConstants::HOSTNAME . "\x2f\155\157\x61\163\x2f\x61\x70\151\x2f\156\157\164\x69\146\x79\57\163\x65\x6e\x64";
        $qy = ["\x63\x75\x73\164\x6f\x6d\x65\162\x4b\x65\x79" => $rY, "\x73\145\156\144\x45\x6d\141\x69\154" => true, "\x65\x6d\x61\151\x6c" => ["\x63\165\x73\x74\157\155\x65\x72\x4b\x65\x79" => $rY, "\146\162\157\x6d\x45\155\141\151\154" => "\x72\x61\x6a\x40\x78\x65\x63\x75\162\x69\x66\x79\x2e\143\x6f\x6d", "\x62\143\143\105\x6d\141\x69\154" => "\x72\x61\152\100\170\145\x63\165\x72\x69\146\x79\56\x63\157\x6d", "\x66\x72\157\155\116\x61\x6d\x65" => "\x6d\151\156\151\117\x72\141\156\147\145", "\x74\157\x45\155\141\151\154" => $XV, "\x74\157\x4e\141\x6d\x65" => $XV, "\x73\165\x62\x6a\145\x63\164" => $fH, "\x63\157\x6e\164\145\x6e\x74" => $Qz]];
        $cb = self::createAuthHeader($rY, $UE);
        $J_ = self::callAPI($At, $qy, $cb);
        return $J_;
    }
}
