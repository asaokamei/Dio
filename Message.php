<?php

class Message
{
    // ---------------------------------
    /**
     *	for messages and error controls
     */
    const  MESSAGE      = 0;
    const  NOTICE       = 1;
    const  ERROR        = 2;
    const  CRITICAL     = 3;
    const  FATAL        = 4;
    var    $msg_array    = array();
    var    $error_level = 0;
    var    $disp_func   = FALSE;
    function __construct() {

    }
    // +-------------------------------------------------------------+
    /**
     *	set message with error level.
     *	for messages with normal level, it saves all messages.
     *	for messages above notice leve, it saves only if
     *	level is higher than the current error level.
     */
    function setMessage( $msg, $level ) {
        if( $level == self::MESSAGE && $this->errorLevel() == self::MESSAGE ) {
            // save the all message.
            $this->msg_array[] = $msg;
        }
        else
            if( $level > $this->errorLevel() ) {
                // saves only the last message.
                $this->msg_array = array( $msg );
                $this->errorLevel( $level );
            }
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     *	returns message.
     */
    function getMessage( $glue='<br />' ) {
        if( $glue === FALSE ) {
            $ret = $this->msg_array;
        }
        else {
            $ret = implode( $glue, $this->msg_array );
        }
        return $ret;
    }
    // +-------------------------------------------------------------+
    /**
     *	saves message.
     */
    function message( $msg ) {
        return $this->setMessage( $msg, self::MESSAGE );
    }
    // +-------------------------------------------------------------+
    /**
     *	saves notice message.
     */
    function notice( $msg ) {
        return $this->setMessage( $msg, self::NOTICE );
    }
    // +-------------------------------------------------------------+
    /**
     *	saves error message.
     */
    function error( $msg ) {
        return $this->setMessage( $msg, self::ERROR );
    }
    // +-------------------------------------------------------------+
    /**
     *	saves ciritcal error message.
     */
    function critical( $msg ) {
        return $this->setMessage( $msg, self::CRITICAL );
    }
    // +-------------------------------------------------------------+
    /**
     *	saves fatal error message.
     */
    function fatal( $msg ) {
        return $this->setMessage( $msg, self::FATAL );
    }
    // +--------------------------------------------------------------- +
    /**
     *	set or get error level.
     */
    function errorLevel( $level=FALSE ) {
        if( $level !== FALSE && is_numeric( $level ) ) {
            $this->error_level = $level;
        }
        return $this->error_level;
    }
    // +--------------------------------------------------------------- +
    /**
     *	checks if error occured (error level above ERROR).
     */
    function isError() {
        return $this->error_level >= self::ERROR;
    }
    // +--------------------------------------------------------------- +
    /**
     *	checks if critical error occured (error level above CRITICAL).
     */
    function isCritical() {
        return $this->error_level >= self::CRITICAL;
    }
    // +--------------------------------------------------------------- +
    /**
     *	displays messages.
     *	specify function to display message in $this->disp_func.
     *	if not set, uses self::disp_message.
     */
    function display( $options=NULL ) {
        if( $this->disp_func ) {
            return call_user_func(
                $this->disp_func,
                $this->msg_array, $this->error_level, $options
            );
        }
        else {
            return self::disp_message( $this->msg_array, $this->error_level, $options );
        }
    }
    // +-------------------------------------------------------------+
    /**
     *	default function to display message.
     *	overwrite this method or specify function in $this->disp_func.
     *
     *	@param array	$msg         array of messages
     *	@param int   	$err_level   error level
     *	@param mix   	$options     from user input
     */
    static function disp_message( $msg, $err_level, $options ) {
        if( !\CenaDta\Util\Util::isValue( $msg ) ) return;

        if( is_array( $msg ) ) $msg = implode( '<br />', $msg );

        if( $err_level > 0 ) {
            $tbl_class = 'tblErr';
            $tbl_msg   = 'エラーがありました';
        }
        else
            if( $err_level > self::MESSAGE ) {
                $tbl_class = 'tblErr';
                $tbl_msg   = '確認してください';
            }
            else {
                $tbl_class = 'tblMsg';
                $tbl_msg   = 'メッセージ';
            }
        if( is_array( $options ) ) extract( $options );
        if( !isset( $width ) || !\CenaDta\Util\Util::isValue( $width ) ) $width = '90%';

        ?>
    <br>
    <table class="<?php echo $tbl_class ?>" width="<?php echo $width; ?>"  border="0" align="center" cellpadding="2" cellspacing="2">
        <tr>
            <th align="center"><?php echo "<font color=white><b>{$tbl_msg}</b></font>\n"; ?></th>
        </tr>
        <tr>
            <td bgcolor="#FFFFFF">
                <?php echo $msg;?>
            </td>
        </tr>
    </table>
    <br>
    <?php
    }
	// +-------------------------------------------------------------+
}