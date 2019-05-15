<?php
namespace formedit\inc;
/**
 * Class session
 */
class session
{

    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new session();
        }
        return self::$instance;
    }


    private $_sGroup='formedit';

    /**
     * session constructor.
     */
    public function __construct()
    {
        defined('_FORMEDIT_EDITOR') or define('_FORMEDIT_EDITOR', 0);
        $sName = "formedit_interpreter";
        if(_FORMEDIT_EDITOR==1)
        {
            $sName = 'formedit_editor';
        }
        @session_name($sName);
        @session_start();
        $this->init();
    }

    /**
     * init new session or session scope
     */
    public function init()
    {
        if(!isset($_SESSION[$this->_sGroup]))
            $_SESSION[$this->_sGroup]=[];
    }

    /**
     * delete session or session scope
     */
    public function destroy()
    {
        unset($_SESSION[$this->_sGroup]);
    }

    /**
     * @return string
     */
    public function getSessionName()
    {
        return session_name();
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return session_id();
    }

    /**
     * @param $sKey
     * @return null
     */
    public function getValue($sKey)
    {
        if(isset($_SESSION[$this->_sGroup][$sKey]))
            return $_SESSION[$this->_sGroup][$sKey];
        return null;
    }

    /**
     * @param $sKey
     * @param $sValue
     */
    public function setValue($sKey, $sValue)
    {
        $_SESSION[$this->_sGroup][$sKey]=$sValue;
    }

    /**
     * @param $sKey
     */
    public function deleteValue($sKey)
    {
        unset($_SESSION[$this->_sGroup][$sKey]);
    }


    /**
     * @return \formedit\core\project|null
     */
    public function getProject($sProjectId)
    {
        $oProject = $this->getValue($sProjectId);
        return $oProject;
    }

    /**
     * @param \formedit\core\project $oProject
     */
    public function setProject($oProject)
    {
        if($oProject===null)
            return;
        $this->setValue($oProject->getId(),$oProject);
    }
}