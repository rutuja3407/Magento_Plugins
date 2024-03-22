<?php


namespace MiniOrange\SP\Helper;

use MiniOrange\SP\Helper\SPConstants;
class Curl
{
    public static function create_customer($fx, $OF, $Ya, $Rs = '', $j1 = '', $j4 = '')
    {
        $JY = SPConstants::HOSTNAME . "\x2f\x6d\157\x61\163\57\x72\145\163\x74\57\x63\165\163\164\157\x6d\145\162\57\141\144\x64";
        $rP = SPConstants::DEFAULT_CUSTOMER_KEY;
        $Io = SPConstants::DEFAULT_API_KEY;
        $q0 = array("\143\157\155\x70\x61\156\171\116\141\155\145" => $OF, "\141\x72\x65\x61\x4f\x66\x49\156\x74\145\162\x65\163\x74" => SPConstants::AREA_OF_INTEREST, "\146\x69\x72\163\x74\x6e\x61\155\145" => $j1, "\x6c\141\x73\164\x6e\x61\155\x65" => $j4, "\x65\x6d\141\x69\x6c" => $fx, "\x70\150\x6f\156\145" => $Rs, "\160\x61\163\x73\x77\157\x72\144" => $Ya);
        $ai = self::createAuthHeader($rP, $Io);
        $qm = self::callAPI($JY, $q0, $ai);
        return $qm;
    }
    public static function get_customer_key($fx, $Ya)
    {
        $JY = SPConstants::HOSTNAME . "\x2f\x6d\x6f\x61\x73\57\x72\x65\x73\x74\x2f\x63\x75\x73\164\x6f\155\145\x72\x2f\153\145\x79";
        $rP = SPConstants::DEFAULT_CUSTOMER_KEY;
        $Io = SPConstants::DEFAULT_API_KEY;
        $q0 = array("\145\155\141\151\154" => $fx, "\160\x61\x73\163\167\157\162\x64" => $Ya);
        $ai = self::createAuthHeader($rP, $Io);
        $qm = self::callAPI($JY, $q0, $ai);
        return $qm;
    }
    public static function check_customer($fx)
    {
        $JY = SPConstants::HOSTNAME . "\x2f\x6d\157\141\163\x2f\162\145\x73\164\x2f\143\x75\x73\164\x6f\155\145\x72\x2f\x63\x68\x65\143\x6b\55\151\x66\x2d\x65\x78\151\163\164\x73";
        $rP = SPConstants::DEFAULT_CUSTOMER_KEY;
        $Io = SPConstants::DEFAULT_API_KEY;
        $q0 = array("\x65\x6d\141\151\x6c" => $fx);
        $ai = self::createAuthHeader($rP, $Io);
        $qm = self::callAPI($JY, $q0, $ai);
        return $qm;
    }
    public static function mo_send_otp_token($dD, $fx = '', $Rs = '')
    {
        $JY = SPConstants::HOSTNAME . "\57\155\x6f\x61\163\57\x61\160\151\57\141\165\164\150\57\143\x68\141\154\x6c\145\x6e\147\x65";
        $rP = SPConstants::DEFAULT_CUSTOMER_KEY;
        $Io = SPConstants::DEFAULT_API_KEY;
        $q0 = array("\x63\165\x73\164\157\x6d\x65\x72\113\145\x79" => $rP, "\145\155\x61\x69\154" => $fx, "\160\x68\157\x6e\145" => $Rs, "\141\165\x74\x68\124\x79\x70\x65" => $dD, "\164\x72\x61\x6e\x73\141\x63\164\x69\157\x6e\116\x61\x6d\145" => SPConstants::AREA_OF_INTEREST);
        $ai = self::createAuthHeader($rP, $Io);
        $qm = self::callAPI($JY, $q0, $ai);
        return $qm;
    }
    public static function validate_otp_token($Mq, $y7)
    {
        $JY = SPConstants::HOSTNAME . "\x2f\155\x6f\141\163\57\141\x70\151\x2f\x61\x75\x74\x68\x2f\x76\141\x6c\x69\144\x61\x74\145";
        $rP = SPConstants::DEFAULT_CUSTOMER_KEY;
        $Io = SPConstants::DEFAULT_API_KEY;
        $q0 = array("\164\x78\111\144" => $Mq, "\x74\x6f\153\145\x6e" => $y7);
        $ai = self::createAuthHeader($rP, $Io);
        $qm = self::callAPI($JY, $q0, $ai);
        return $qm;
    }
    public static function submit_contact_us($JT, $q2, $dU, $GC)
    {
        $JY = SPConstants::HOSTNAME . "\x2f\155\x6f\141\163\57\162\x65\x73\x74\x2f\x63\165\163\164\x6f\155\x65\x72\x2f\143\x6f\x6e\164\x61\x63\164\55\x75\x73";
        $dU = "\x5b" . SPConstants::AREA_OF_INTEREST . "\x5d\x3a\40" . $dU;
        $rP = SPConstants::DEFAULT_CUSTOMER_KEY;
        $Io = SPConstants::DEFAULT_API_KEY;
        $q0 = array("\143\157\155\160\141\x6e\x79" => $GC, "\145\x6d\141\x69\154" => $JT, "\x70\x68\157\156\145" => $q2, "\x71\165\145\162\x79" => $dU, "\x63\143\105\x6d\141\151\154" => "\x6d\141\x67\x65\156\164\157\163\x75\x70\x70\157\162\x74\x40\x78\x65\143\x75\x72\x69\146\171\x2e\143\157\155");
        $ai = self::createAuthHeader($rP, $Io);
        $qm = self::callAPI($JY, $q0, $ai);
        return TRUE;
    }
    public static function forgot_password($fx, $rP, $Io)
    {
        $JY = SPConstants::HOSTNAME . "\57\x6d\157\141\x73\x2f\x72\145\x73\164\57\143\165\163\x74\x6f\x6d\145\x72\57\x70\141\x73\163\x77\x6f\162\144\55\x72\x65\x73\x65\164";
        $q0 = array("\x65\155\x61\151\154" => $fx);
        $ai = self::createAuthHeader($rP, $Io);
        $qm = self::callAPI($JY, $q0, $ai);
        return $qm;
    }
    public static function check_customer_ln($rP, $Io)
    {
        $JY = SPConstants::HOSTNAME . "\x2f\x6d\x6f\141\163\x2f\x72\145\163\x74\57\143\165\163\x74\157\155\145\x72\x2f\154\151\143\x65\156\x73\x65";
        $q0 = array("\143\165\x73\x74\157\155\145\x72\111\144" => $rP, "\141\x70\x70\154\x69\x63\x61\164\x69\x6f\156\116\x61\155\x65" => SPConstants::APPLICATION_NAME, "\x6c\x69\x63\145\156\x73\x65\124\171\x70\x65" => !MoUtility::micr() ? "\x44\x45\115\117" : "\x50\x52\105\115\111\125\x4d");
        $ai = self::createAuthHeader($rP, $Io);
        $qm = self::callAPI($JY, $q0, $ai);
        return $qm;
    }
    private static function createAuthHeader($rP, $Io)
    {
        $q1 = round(microtime(true) * 1000);
        $q1 = number_format($q1, 0, '', '');
        $GO = $rP . $q1 . $Io;
        $ai = hash("\163\150\x61\65\x31\x32", $GO);
        $Z2 = array("\x43\157\156\164\x65\x6e\164\55\x54\171\x70\145\72\40\x61\x70\160\x6c\151\x63\141\x74\151\x6f\x6e\x2f\x6a\163\157\156", "\x43\x75\163\164\x6f\155\x65\162\55\113\145\x79\x3a\40{$rP}", "\x54\151\155\145\163\164\x61\155\x70\x3a\40{$q1}", "\x41\165\164\150\x6f\x72\151\172\141\164\151\157\156\72\x20{$ai}");
        return $Z2;
    }
    private static function callAPI($JY, $T6 = array(), $FD = array("\103\x6f\156\x74\145\x6e\164\55\x54\171\x70\x65\72\x20\x61\160\x70\154\151\x63\141\x74\151\157\156\x2f\152\163\x6f\156"))
    {
        $gf = new MoCurl();
        $jp = array("\x43\125\x52\114\x4f\x50\x54\x5f\106\x4f\114\114\117\127\x4c\x4f\103\x41\x54\111\117\x4e" => true, "\103\x55\122\x4c\117\120\124\137\105\116\x43\x4f\104\x49\x4e\x47" => '', "\103\x55\x52\x4c\117\x50\x54\x5f\x52\105\124\125\122\x4e\x54\122\101\x4e\x53\x46\105\x52" => true, "\x43\x55\x52\114\x4f\120\124\x5f\101\125\124\x4f\x52\x45\106\105\122\x45\x52" => true, "\x43\x55\x52\x4c\x4f\120\124\x5f\x54\111\x4d\x45\x4f\x55\x54" => 0, "\103\x55\x52\114\x4f\x50\124\137\115\x41\130\x52\105\104\111\x52\123" => 10);
        $bs = !empty($T6) ? "\x50\x4f\x53\x54" : "\x47\105\124";
        $gf->setConfig($jp);
        $gf->write($bs, $JY, "\61\56\61", $FD, !empty($T6) ? json_encode($T6) : '');
        $Ge = $gf->read();
        $gf->close();
        return $Ge;
    }
    public static function ccl($rP, $Io)
    {
        $JY = SPConstants::HOSTNAME . "\x2f\155\157\141\x73\x2f\162\145\x73\x74\57\x63\165\x73\x74\x6f\155\145\x72\57\154\151\143\145\156\163\x65";
        $q0 = array("\143\x75\163\x74\157\155\x65\x72\111\x64" => $rP, "\x61\x70\160\154\151\x63\141\164\x69\157\x6e\x4e\141\x6d\145" => SPConstants::LICENSE_PLAN);
        $ai = self::createAuthHeader($rP, $Io);
        $qm = self::callAPI($JY, $q0, $ai);
        return $qm;
    }
    public static function vml($rP, $Io, $qk, $qv)
    {
        $JY = SPConstants::HOSTNAME . "\57\155\x6f\x61\x73\57\x61\160\x69\57\142\x61\143\x6b\x75\x70\x63\x6f\144\145\57\x76\x65\162\x69\x66\x79";
        $q0 = array("\143\x6f\x64\145" => $qk, "\x63\x75\x73\164\x6f\x6d\145\x72\113\x65\x79" => $rP, "\141\144\144\x69\164\151\x6f\156\141\154\x46\x69\145\154\x64\163" => array("\x66\x69\x65\154\144\x31" => $qv));
        $ai = self::createAuthHeader($rP, $Io);
        $qm = self::callAPI($JY, $q0, $ai);
        return $qm;
    }
    public static function mius($rP, $Io, $qk)
    {
        $JY = SPConstants::HOSTNAME . "\x2f\155\157\141\163\x2f\x61\x70\x69\x2f\x62\x61\x63\153\x75\160\x63\x6f\144\145\x2f\165\160\144\141\164\x65\163\164\141\x74\x75\x73";
        $q0 = array("\x63\x6f\x64\145" => $qk, "\x63\165\x73\164\157\x6d\145\162\x4b\x65\171" => $rP);
        $ai = self::createAuthHeader($rP, $Io);
        $qm = self::callAPI($JY, $q0, $ai);
        return $qm;
    }
    public static function update_status($rP, $Io, $qk, $qv)
    {
        $JY = SPConstants::HOSTNAME . "\x2f\x6d\x6f\x61\163\x2f\141\160\151\57\142\141\x63\153\x75\x70\143\157\144\x65\57\x75\x70\x64\141\x74\145\163\x74\x61\x74\x75\x73";
        $q0 = array("\x63\157\x64\145" => $qk, "\143\165\163\164\157\x6d\x65\162\x4b\145\171" => $rP, "\x61\x64\144\x69\164\151\x6f\x6e\141\154\x46\x69\145\x6c\x64\163" => array("\146\151\145\154\144\x31" => $qv));
        $ai = self::createAuthHeader($rP, $Io);
        $qm = self::callAPI($JY, $q0, $ai);
        return $qm;
    }
    public static function submit_to_magento_team($JT, $ph, $Ge)
    {
        $Ge = implode("\x2c\40", $Ge);
        $JY = SPConstants::HOSTNAME . "\57\x6d\x6f\x61\163\57\x61\x70\x69\57\156\157\x74\x69\x66\x79\57\x73\x65\156\x64";
        $rP = SPConstants::DEFAULT_CUSTOMER_KEY;
        $Io = SPConstants::DEFAULT_API_KEY;
        $Ww = array("\x63\x75\163\x74\x6f\x6d\x65\x72\113\x65\171" => $rP, "\x73\x65\x6e\144\x45\155\x61\151\154" => true, "\x65\155\x61\x69\154" => array("\x63\x75\163\x74\x6f\x6d\145\x72\113\x65\171" => $rP, "\146\x72\157\155\x45\155\141\151\x6c" => "\x6e\151\164\x65\163\150\56\x70\141\155\156\x61\156\151\x40\x78\x65\x63\165\162\151\x66\x79\x2e\x63\x6f\x6d", "\x62\143\143\105\x6d\141\151\154" => "\162\165\x74\x75\x6a\x61\56\163\157\x6e\141\167\x61\x6e\x65\x40\x78\145\143\165\162\x69\146\171\56\x63\157\x6d", "\x66\162\157\x6d\116\141\x6d\145" => "\x6d\x69\x6e\x69\117\162\x61\156\x67\145", "\x74\157\x45\x6d\x61\x69\x6c" => "\x6e\151\164\x65\163\x68\x2e\x70\x61\x6d\156\x61\156\x69\100\170\145\143\x75\x72\x69\x66\171\56\x63\x6f\x6d", "\x74\157\x4e\x61\x6d\145" => "\116\x69\164\x65\163\150", "\x73\165\142\152\145\x63\x74" => "\x4d\x61\147\x65\x6e\x74\157\x20\62\x2e\60\x20\x53\101\x4d\x4c\40\123\x50\x20\124\x72\x69\141\154\x20\120\154\165\147\x69\x6e\40{$ph}\x20\72\40{$JT}", "\x63\x6f\x6e\x74\x65\156\x74" => $Ge));
        $Lm = array("\x63\x75\163\x74\x6f\x6d\145\162\x4b\x65\x79" => $rP, "\163\x65\156\144\x45\x6d\x61\x69\x6c" => true, "\x65\155\x61\151\154" => array("\x63\x75\163\164\x6f\155\x65\x72\113\145\x79" => $rP, "\146\162\157\x6d\x45\155\141\x69\154" => "\162\x75\163\x68\151\153\145\x73\x68\56\x6e\151\153\x61\x6d\x40\x78\x65\x63\x75\162\151\146\171\56\x63\157\x6d", "\142\143\x63\105\155\x61\x69\154" => "\162\141\x6a\100\170\145\143\165\x72\x69\x66\171\x2e\143\157\155", "\146\x72\157\x6d\116\141\x6d\x65" => "\155\x69\x6e\151\x4f\x72\x61\x6e\x67\145", "\164\157\105\x6d\141\x69\154" => "\162\x75\163\x68\x69\153\x65\x73\x68\56\156\x69\x6b\x61\155\x40\x78\145\143\165\x72\151\146\x79\56\143\157\155", "\164\x6f\116\x61\x6d\145" => "\x52\165\x73\x68\151\153\145\x73\x68", "\163\x75\x62\x6a\x65\x63\164" => "\115\141\147\x65\x6e\164\x6f\40\62\56\x30\x20\123\101\115\114\x20\x53\120\x20\124\162\x69\x61\x6c\40\120\154\165\x67\151\x6e\40{$ph}\x20\72\40{$JT}", "\x63\157\156\x74\145\x6e\x74" => $Ge));
        $Pe = json_encode($Ww);
        $HP = json_encode($Lm);
        $ai = self::createAuthHeader($rP, $Io);
        $PM = self::callAPI($JY, $Ww, $ai);
        $Vb = self::callAPI($JY, $Lm, $ai);
        return true;
    }
    public static function notify($rP, $Io, $SF, $Ge, $XP)
    {
        $JY = SPConstants::HOSTNAME . "\x2f\x6d\157\141\x73\57\141\160\151\57\156\157\164\x69\x66\x79\57\163\x65\x6e\144";
        $q0 = ["\143\x75\163\x74\157\x6d\x65\162\x4b\x65\171" => $rP, "\163\145\156\x64\105\x6d\141\x69\x6c" => true, "\x65\155\x61\151\154" => ["\x63\165\x73\164\x6f\x6d\x65\162\113\145\171" => $rP, "\x66\x72\x6f\155\105\155\141\x69\x6c" => "\x72\141\x6a\x40\170\x65\x63\x75\x72\151\x66\171\x2e\143\x6f\x6d", "\142\143\x63\x45\155\x61\x69\x6c" => "\162\141\x6a\100\x78\x65\x63\x75\x72\x69\x66\171\x2e\143\157\155", "\x66\x72\157\155\116\141\155\145" => "\155\151\x6e\151\x4f\162\141\x6e\147\x65", "\164\x6f\x45\x6d\x61\151\x6c" => $SF, "\164\x6f\116\x61\x6d\x65" => $SF, "\x73\165\x62\x6a\145\x63\x74" => $XP, "\x63\x6f\156\164\145\x6e\x74" => $Ge]];
        $ai = self::createAuthHeader($rP, $Io);
        $qm = self::callAPI($JY, $q0, $ai);
        return $qm;
    }
}
