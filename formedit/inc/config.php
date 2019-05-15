<?php
namespace formedit\inc;

class config
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new config();
        }
        return self::$instance;
    }

    /**
     * @return string
     */
    public function getSystemPathReal()
    {
        return realpath(__DIR__."/../../.");
    }

    /**
     * config constructor.
     */
    public function __construct()
    {
        //load settings
        require_once(__DIR__."/../../config.inc.php");
    }


    public $url="";
    public $fileBrowser="";
    public $projectConnection=[];

    /**
     * @return string
     */
    public function getBrowserPathReal()
    {
        return realpath(__DIR__."/../../".$this->getBrowserPath());
    }

    /**
     * @return mixed
     */
    public function getBrowserPath()
    {
        return $this->fileBrowser[$this->getCurrentFileBrowserConfig()]['path'];
    }

    /**
     * @return mixed
     */
    public function getBrowserUrl()
    {
        return $this->fileBrowser[$this->getCurrentFileBrowserConfig()]['url'];
    }

    /**
     * @return array
     */
    public function getBrowserExtensions()
    {
        return $this->fileBrowser[$this->getCurrentFileBrowserConfig()]['ext'];
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return trim(trim($this->url),"/")."/";
    }


    protected $_aProjectConnections=null;
    /**
     * @var \core\project $oProject
     * @return \formedit\core\interfaces\connection[]
     */
    public function getProjectConnections($oProject)
    {
        if($this->_aProjectConnections===null)
        {
            $this->_aProjectConnections=[];

            foreach($this->projectConnection as $sKey => $aData)
            {
                $sName = $aData['name'];
                $sClass = $aData['class'];
                /**
                 * @var \formedit\core\interfaces\connection $o
                 */
                $o = new $sClass($oProject, $sName, $aData['properties']);
                $this->_aProjectConnections[]=$o;
            }
        }
        return $this->_aProjectConnections;
    }


    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function getRequestParameter($name, $default = null)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        } elseif (isset($_GET[$name])) {
            return $_GET[$name];
        } elseif (isset($_REQUEST[$name])) {
            return $_REQUEST[$name];
        } else {
            return $default;
        }
    }


    protected $_sCurrentProjectId="";
    public function setCurrentProjectId($sProjectId)
    {
        $this->_sCurrentProjectId = $sProjectId;
    }

    /**
     * @return \formedit\core\project|null
     */
    public function getCurrentProject()
    {
        return \formedit\inc\session::getInstance()->getProject($this->_sCurrentProjectId);
    }


    protected $_sCurrentFormId="";
    public function setCurrentFormId($sFormId)
    {
        $this->_sCurrentFormId = $sFormId;
    }

    /**
     * @return \formedit\core\form|null
     */
    public function getCurrentForm()
    {
        if($oProject = $this->getCurrentProject())
        {
            /**
             * @var \formedit\core\form $oForm
             */
            $oForm = $oProject->getChild($this->_sCurrentFormId);
            return $oForm;
        }
        return null;
    }

    /**
     * @return string
     */
    public function getCurrentFileBrowserConfig()
    {
        $sConfig = $this->getRequestParameter('filebrowserconfig');
        if($sConfig=="")
            $sConfig="editor";
        return $sConfig;
    }
}