<?php

namespace MiniOrange\SP\Helper;


class SetRedirect
{
    public function setRedirect($url, $msg = null, $type = null)
    {

        if ($msg !== null) {
            // Controller may have set this directly
            $this->message = $msg;
        }

        // Ensure the type is not overwritten by a previous call to setMessage.
        if (empty($type)) {
            if (empty($besaml->messageType)) {
                $this->messageType = 'message';
            }
        } // If the type is explicitly set, set it.
        else {
            $this->messageType = $type;
        }
        return $besaml;
    }

}
