<?php 
namespace formedit\core\interfaces;

use \formedit;
use \formedit\core\property;

abstract class control extends \formedit\core\interfaces\base
{

#region "properties"

    public function setDefault(\formedit\core\form $oForm)
    {
        $this->getProperty('System/Dimension/Position')->setValue($oForm->getProperty('Default/Dimension/Position')->getValue());
        $this->getProperty('System/Dimension/Absolute/Width')->setValue($oForm->getProperty('Default/Dimension/Absolute/Width')->getValue());
        $this->getProperty('System/Dimension/Absolute/Height')->setValue($oForm->getProperty('Default/Dimension/Absolute/Height')->getValue());
        $this->getProperty('System/Dimension/Relative/Margin top')->setValue($oForm->getProperty('Default/Dimension/Relative/Margin top')->getValue());
        $this->getProperty('System/Dimension/Relative/Margin left')->setValue($oForm->getProperty('Default/Dimension/Relative/Margin left')->getValue());
        $this->getProperty('System/Dimension/Relative/Margin right')->setValue($oForm->getProperty('Default/Dimension/Relative/Margin right')->getValue());
        $this->getProperty('System/Dimension/Relative/Margin bottom')->setValue($oForm->getProperty('Default/Dimension/Relative/Margin bottom')->getValue());
        $this->getProperty('System/Dimension/Relative/Width')->setValue($oForm->getProperty('Default/Dimension/Relative/Width')->getValue());
        $this->getProperty('System/Dimension/Relative/Height')->setValue($oForm->getProperty('Default/Dimension/Relative/Height')->getValue());
        $this->getProperty('System/Dimension/Relative/Float')->setValue($oForm->getProperty('Default/Dimension/Relative/Float')->getValue());
        $this->getProperty('System/Dimension/Fill/Margin top')->setValue($oForm->getProperty('Default/Dimension/Fill/Margin top')->getValue());
        $this->getProperty('System/Dimension/Fill/Margin left')->setValue($oForm->getProperty('Default/Dimension/Fill/Margin left')->getValue());
        $this->getProperty('System/Dimension/Fill/Margin right')->setValue($oForm->getProperty('Default/Dimension/Fill/Margin right')->getValue());
        $this->getProperty('System/Dimension/Fill/Margin bottom')->setValue($oForm->getProperty('Default/Dimension/Fill/Margin bottom')->getValue());
    }


    public function loadProperties()
    {
        $this->addProperty(new property('System/Standard/Type','control', property::TYPE_label ));
        $this->addProperty(new property('System/Standard/Parent','', property::TYPE_labelparent));
        $this->addProperty(new property('System/Standard/Control id',uniqid(""), property::TYPE_textbox ));

        $this->addProperty(new property('System/Dimension/Position','absolute', property::TYPE_selectboxSwitch, 'How should the element position', ['absolute' => 'Absolute', 'relative' => 'Relative', 'fill' => 'Fill'], 'System/Dimension'));
        $this->addProperty(new property('System/Dimension/Absolute/Top','0px', property::TYPE_textbox, "",null, 'System/Dimension', 'absolute'));
        $this->addProperty(new property('System/Dimension/Absolute/Left', "0px", property::TYPE_textbox, "",null, 'System/Dimension', 'absolute'));
        $this->addProperty(new property('System/Dimension/Absolute/Width',"200px", property::TYPE_textbox, "",null, 'System/Dimension', 'absolute'));
        $this->addProperty(new property('System/Dimension/Absolute/Height',"30px", property::TYPE_textbox, "",null, 'System/Dimension', 'absolute'));
        $this->addProperty(new property('System/Dimension/Relative/Margin','', property::TYPE_textboxPopulate, "Copy value to margin top, bottom, right, left",['System/Dimension/Relative/Margin top','System/Dimension/Relative/Margin left', 'System/Dimension/Relative/Margin right','System/Dimension/Relative/Margin bottom'], 'System/Dimension', 'relative'));
        $this->addProperty(new property('System/Dimension/Relative/Margin top','0px', property::TYPE_textbox, "",null, 'System/Dimension', 'relative'));
        $this->addProperty(new property('System/Dimension/Relative/Margin left', "auto", property::TYPE_textbox, "",null, 'System/Dimension', 'relative'));
        $this->addProperty(new property('System/Dimension/Relative/Margin right',"auto", property::TYPE_textbox, "",null, 'System/Dimension', 'relative'));
        $this->addProperty(new property('System/Dimension/Relative/Margin bottom',"0px", property::TYPE_textbox, "",null, 'System/Dimension', 'relative'));
        $this->addProperty(new property('System/Dimension/Relative/Width',"100%", property::TYPE_textbox, "",null, 'System/Dimension', 'relative'));
        $this->addProperty(new property('System/Dimension/Relative/Height',"30px", property::TYPE_textbox, "",null, 'System/Dimension', 'relative'));
        $this->addProperty(new property('System/Dimension/Relative/Float',"none", property::TYPE_selectbox, "",['none' => 'None', 'left' => 'Left', 'right' => 'Right'], 'System/Dimension', 'relative'));
        $this->addProperty(new property('System/Dimension/Fill/Margin','', property::TYPE_textboxPopulate, "Copy value to margin top, bottom, right, left",['System/Dimension/Fill/Margin top','System/Dimension/Fill/Margin left', 'System/Dimension/Fill/Margin right','System/Dimension/Fill/Margin bottom'], 'System/Dimension', 'fill'));
        $this->addProperty(new property('System/Dimension/Fill/Margin top','0px', property::TYPE_textbox, "",null, 'System/Dimension', 'fill'));
        $this->addProperty(new property('System/Dimension/Fill/Margin left', "0px", property::TYPE_textbox, "",null, 'System/Dimension', 'fill'));
        $this->addProperty(new property('System/Dimension/Fill/Margin right',"0px", property::TYPE_textbox, "",null, 'System/Dimension', 'fill'));
        $this->addProperty(new property('System/Dimension/Fill/Margin bottom',"0px", property::TYPE_textbox, "",null, 'System/Dimension', 'fill'));

        $this->addProperty(new property('System/Style/Tab index',100, property::TYPE_textboxIntegerAndNegative, 'Define the tab order'));
        $this->addProperty(new property('System/Style/Z-index',1000, property::TYPE_textboxIntegerAndNegative));
        $this->addProperty(new property('System/Style/Visible',1, property::TYPE_checkbox));
        $this->addProperty(new property('System/Style/Css','', property::TYPE_textbox));

        $this->addProperty(new property('System/Editor/Min. height',10, property::TYPE_textboxInteger, 'Minimum width in px'), false);
        $this->addProperty(new property('System/Editor/Min. width',10, property::TYPE_textboxInteger, 'Minimum width in px'), false);
        $this->addProperty(new property('System/Editor/Background color','gold', property::TYPE_labelcolor), false);
        $this->addProperty(new property('System/Editor/Background color (selected)','goldenrod', property::TYPE_labelcolor), false);
        $this->addProperty(new property('System/Editor/Background color (drop)','darkgoldenrod', property::TYPE_labelcolor), false);

        //fallback sidebar group
        $this->addProperty(new property('System/Standard/Group name','Style'));

        //fallback control contain a container
        $this->addProperty(new property('System/Standard/Has container',false, property::TYPE_label,  'Can the control host other controls'));

        parent::loadProperties();
    }

    /**
     * @param $aData
     */
    public function setProperties($aData)
    {
        parent::setProperties($aData);
    }

    /**
     * @return null|bool
     */
    public function hasContainer()
    {
        $sName="";
        if($oProperty = $this->getProperty('System/Standard/Has container'))
        {
            $sName = $oProperty->getValue();
        }
        return $sName;
    }


    /**
     * @return string
     */
    public function getParentId()
    {
        $sName="";
        if($oProperty = $this->getProperty('System/Standard/Parent'))
        {
            $sName = $oProperty->getValue();
        }
        return $sName;
    }

    /**
     * @param $sId
     */
    public function setParentId($sId)
    {
        $this->getProperty('System/Standard/Parent')->setValue($sId);
    }

    /**
     * @return string
     */
    public function getPositionType()
    {
        return $this->getProperty('System/Dimension/Position')->getValue();
    }

    /**
     * @return int
     */
    public function getMinWidthInteger()
    {
        $i = (int) str_replace('px','',$this->getProperty('System/Editor/Min. width')->getValue());
        if($i < 0)
            $i = 0;
        return $i;
    }

    /**
     * @return int
     */
    public function getMinHeightInteger()
    {
        $i = (int) str_replace('px','',$this->getProperty('System/Editor/Min. height')->getValue());
        if($i < 0)
            $i = 0;
        return $i;
    }

    /**
     * @return int
     */
    public function getLeftInteger()
    {
        $iLeft = (int) str_replace('px','',$this->getProperty('System/Dimension/Absolute/Left')->getValue());
        if($iLeft < 0)
            $iLeft = 0;
        return $iLeft;
    }

    /**
     * @return int
     */
    public function getHeightInteger()
    {
        $i = (int) str_replace('px','',$this->getProperty('System/Dimension/Absolute/Height')->getValue());
        if($i < 0)
            $i = 0;
        return $i;
    }

    /**
     * @return int
     */
    public function getWidthInteger()
    {
        $i = (int) str_replace('px','',$this->getProperty('System/Dimension/Absolute/Width')->getValue());
        if($i < 0)
            $i = 0;
        return $i;
    }

    /**
     * @return int
     */
    public function getTopInteger()
    {
        $iTop = (int) str_replace('px','',$this->getProperty('System/Dimension/Absolute/Top')->getValue());
        if($iTop < 0)
            $iTop = 0;
        return $iTop;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        $sName="";
        if($oProperty = $this->getProperty('System/Standard/Group name'))
        {
            $sName = $oProperty->getValue();
        }
        return $sName;
    }

    /**
     * @param $sLeft
     * @param $sTop
     * @param $sParentId
     */
    public function setPosition($sLeft, $sTop, $sParentId)
    {
        $this->getProperty('System/Dimension/Absolute/Left')->setValue($sLeft);
        $this->getProperty('System/Dimension/Absolute/Top')->setValue($sTop);
        if($sParentId!="")
            $this->getProperty('System/Standard/Parent')->setValue($sParentId);
    }

    /**
     * @param $sWidth
     * @param $sHeight
     */
    public function setDimension($sWidth, $sHeight)
    {
        $this->getProperty('System/Dimension/Absolute/Width')->setValue($sWidth);
        $this->getProperty('System/Dimension/Absolute/Height')->setValue($sHeight);
    }

    /**
     * @param $sWidth
     * @param $sHeight
     */
    public function setDimensionRelative($sWidth, $sHeight)
    {
        $this->getProperty('System/Dimension/Relative/Width')->setValue($sWidth);
        $this->getProperty('System/Dimension/Relative/Height')->setValue($sHeight);
    }

    /**
     * @param $iTabOrder
     */
    public function setTabOrder($iTabOrder)
    {
        if($this->getProperty('System/Style/Tab index'))
            $this->getProperty('System/Style/Tab index')->setValue($iTabOrder);
    }
#endregion


#region "Editor Sidebar"
    /**
     * @return string
     */
    public function getHtmlEditorSidebar()
    {
        return '<div 
            data-draggable 
            data-controlclass="'.$this->getClassName().'" 
            data-searchtext="'.strtolower($this->getName()).'"
            class="control"
            style="
            background-color:'.$this->getProperty('System/Editor/Background color')->getValue().';
            ">
        '.$this->getName().'
        </div>';
    }
#endregion


#region "Editor"
    /**
     * @return string
     */
    public function getHtmlEditorDesktopText()
    {
        return $this->getName();
    }


    /**
     * @return string
     */
    public function getHtmlEditorDesktopCss()
    {
        $sCss="";
        if($this->getPositionType()=="absolute")
        {
            $sCss = 'left:'.$this->getProperty('System/Dimension/Absolute/Left')->getValue().';
            top:'.$this->getProperty('System/Dimension/Absolute/Top')->getValue().';
            width:'.$this->getProperty('System/Dimension/Absolute/Width')->getValue().';
            height:'.$this->getProperty('System/Dimension/Absolute/Height')->getValue().'; ';
        }
        elseif($this->getPositionType()=="relative")
        {
            $sCss = 'float:'.$this->getProperty('System/Dimension/Relative/Float')->getValue().';
            height:'.$this->getProperty('System/Dimension/Relative/Height')->getValue().';
            width:'.$this->getProperty('System/Dimension/Relative/Width')->getValue().';
            margin:'.$this->getProperty('System/Dimension/Relative/Margin top')->getValue().' '.$this->getProperty('System/Dimension/Relative/Margin right')->getValue().' '.$this->getProperty('System/Dimension/Relative/Margin bottom')->getValue().' '.$this->getProperty('System/Dimension/Relative/Margin left')->getValue().'; ';
        }
        elseif($this->getPositionType()=="fill")
        {
            $sCss = 'width:100%; 
            height:100%; 
            margin:'.$this->getProperty('System/Dimension/Fill/Margin top')->getValue().' '.$this->getProperty('System/Dimension/Fill/Margin right')->getValue().' '.$this->getProperty('System/Dimension/Fill/Margin bottom')->getValue().' '.$this->getProperty('System/Dimension/Fill/Margin left')->getValue().'; ';
        }

        $sCss.= 'z-index:'.$this->getProperty('System/Style/Z-index')->getValue().';
        background-color:'.$this->getProperty('System/Editor/Background color')->getValue().';
        min-height: '.$this->getProperty('System/Editor/Min. height')->getValue().'px; 
        min-width: '.$this->getProperty('System/Editor/Min. width')->getValue().'px; ';

        $sCss.=$this->getProperty('System/Style/Css')->getValue();

        $sCss = trim($sCss);
        if(substr($sCss,strlen($sCss)-1)!=";")
        {
            $sCss.=";";
        }

        return $sCss;
    }


    /**
     * how the control in editor mode looks like
     *
     * @return string
     */
    public function getHtmlEditorDesktop()
    {
        $sHtml="";
        $sHtml.= $this->_getHtmlEditorDesktopHeader();
        $sHtml.= $this->_getHtmlEditorDesktopBody();
        $sHtml.= $this->_getHtmlEditorDesktopFooter();
        return $sHtml;
    }


    /**
     * @return string
     */
    protected function _getHtmlEditorDesktopHeader()
    {
        return '<div 
        class="control '.$this->getPositionType().' control_'.basename(strtolower(str_replace("\\","/",$this->getClassName()))).'"
        data-resizable
        data-controlclass="'.$this->getClassName().'" 
        data-controlname="'.$this->getName().'" 
        data-minheight="'.$this->getProperty('System/Editor/Min. height')->getValue().'"
        data-minwidth="'.$this->getProperty('System/Editor/Min. width')->getValue().'"
        data-backgroundcolor="'.$this->getProperty('System/Editor/Background color')->getValue().'"
        data-backgroundcolorselected="'.$this->getProperty('System/Editor/Background color (selected)')->getValue().'"
        data-backgroundcolordrop="'.$this->getProperty('System/Editor/Background color (drop)')->getValue().'"
        id="'.$this->getId().'"
        style="'.$this->getHtmlEditorDesktopCss().'"
        title="'.$this->getName().'"
        >
        <mover class="mover ui-icon ui-icon-arrow-4"></mover>
        <sizer class="sizer ui-icon ui-icon-gripsmall-diagonal-se"></sizer>
        <insert class="before"></insert>
        <insert class="after"></insert>
        ';

    }


    /**
     * @return string
     */
    protected function _getHtmlEditorDesktopBody()
    {
        $sId=$this->getId();
        if($this->hasContainer())
            $sId = $this->getId()."_0";

        return '<div class="control_body"
        '.$this-> _getHtmlEditorDeskopContainerAttribute($sId).'
        >'.$this->_getHtmlEditorDesktopName($sId,$this->getHtmlEditorDesktopText()).'</div>';
    }


    /**
     * @return string
     */
    protected function _getHtmlEditorDesktopFooter()
    {
        return '</div>';
    }


    /**
     * @param $sId
     * @param $sTitle
     * @return string
     */
    protected function _getHtmlEditorDesktopName($sId, $sTitle)
    {
        return '<title>'.$sTitle.'</title><!--CONTENT_'.$sId.'-->';
    }


    /**
     * @param $sId
     * @return string
     */
    protected function _getHtmlEditorDeskopContainerAttribute($sId)
    {
        return "";
    }


    /**
     * @return string
     */
    public function getCssEditor()
    {
        $path = explode('\\', $this->getClassName());
        $sClass = array_pop($path);

        //@import url('src/css/tooltip.css');
        $sFile = "formedit/addons/controls/".$sClass."/editor.css";
        $sPath = \formedit\inc\config::getInstance()->getSystemPathReal()."/".$sFile;

        if(file_exists($sPath))
        {
            return "@import url('".$sFile."');".PHP_EOL;
        }
    }


    /**
     * @return string
     */
    public function getJsEditor()
    {
        $path = explode('\\', $this->getClassName());
        $sClass = array_pop($path);

        //@import url('src/css/tooltip.css');
        $sFile = "formedit/addons/controls/".$sClass."/editor.js";
        $sPath = \formedit\inc\config::getInstance()->getSystemPathReal()."/".$sFile;

        if(file_exists($sPath))
        {
            return '<script src="'.$sFile.'"></script>'.PHP_EOL;
        }
    }
#endregion


#region "Interpreter"

    /**
     * @param $sId
     * @return string
     */
    protected function _getHtmlInterpreterDeskopContainerAttribute($sId, $sControlId)
    {
        $sHtml = '
        id="'.$sId.'"
        data-control_id="'.$sControlId.'"
        ';
        return $sHtml;
    }


    /**
     * @return string
     */
    public function getHtmlInterpreterDesktopCss()
    {
        $sCss="";
        if($this->getPositionType()=="absolute")
        {
            $sCss = 'left:'.$this->getProperty('System/Dimension/Absolute/Left')->getValue().';
            top:'.$this->getProperty('System/Dimension/Absolute/Top')->getValue().';
            width:'.$this->getProperty('System/Dimension/Absolute/Width')->getValue().';
            height:'.$this->getProperty('System/Dimension/Absolute/Height')->getValue().'; ';
        }
        elseif($this->getPositionType()=="relative")
        {
            $sCss = 'float:'.$this->getProperty('System/Dimension/Relative/Float')->getValue().';
            height:'.$this->getProperty('System/Dimension/Relative/Height')->getValue().';
            width:'.$this->getProperty('System/Dimension/Relative/Width')->getValue().';
            margin:'.$this->getProperty('System/Dimension/Relative/Margin top')->getValue().' '.$this->getProperty('System/Dimension/Relative/Margin right')->getValue().' '.$this->getProperty('System/Dimension/Relative/Margin bottom')->getValue().' '.$this->getProperty('System/Dimension/Relative/Margin left')->getValue().'; ';
        }
        elseif($this->getPositionType()=="fill")
        {
            $sCss = 'width:100%; 
            height:100%; 
            margin:'.$this->getProperty('System/Dimension/Fill/Margin top')->getValue().' '.$this->getProperty('System/Dimension/Fill/Margin right')->getValue().' '.$this->getProperty('System/Dimension/Fill/Margin bottom')->getValue().' '.$this->getProperty('System/Dimension/Fill/Margin left')->getValue().'; ';
        }

        $sCss.= 'z-index:'.$this->getProperty('System/Style/Z-index')->getValue().';
        min-height: '.$this->getProperty('System/Editor/Min. height')->getValue().'px; 
        min-width: '.$this->getProperty('System/Editor/Min. width')->getValue().'px; ';

        $sCss.=$this->getProperty('System/Style/Css')->getValue();

        $sCss = trim($sCss);
        if(substr($sCss,strlen($sCss)-1)!=";")
        {
            $sCss.=";";
        }

        return $sCss;
        /*
        $sCss = 'left:'.$this->getProperty('System/Dimension/Absolute/Left')->getValue().';
        top:'.$this->getProperty('System/Dimension/Absolute/Top')->getValue().';
        width:'.$this->getProperty('System/Dimension/Absolute/Width')->getValue().';
        height:'.$this->getProperty('System/Dimension/Absolute/Height')->getValue().';
        z-index:'.$this->getProperty('System/Style/Z-index')->getValue().';';
        $sCss.=$this->getProperty('System/Style/Css')->getValue();
        return $sCss;
        */
    }


    /**
     * how the control looks like in interpreter mode
     *
     * @return string
     */
    public function getHtmlInterpreterDesktop()
    {
        $sHtml="";
        $sHtml.= $this->_getHtmlInterpreterDesktopHeader();
        $sHtml.= $this->_getHtmlInterpreterDesktopBody();
        $sHtml.= $this->_getHtmlInterpreterDesktopFooter();
        return $sHtml;
    }


    /**
     * @return string
     */
    protected function _getHtmlInterpreterDesktopHeader()
    {
        return '<div 
        data-control_id="'.$this->getProperty('System/Standard/Control id')->getValue().'"
        class="control '.$this->getPositionType().' control_'.basename(strtolower(str_replace("\\","/",$this->getClassName()))).'"
        id="'.$this->getId().'"
        style="'.$this->getHtmlInterpreterDesktopCss().'"
        >';
    }


    /**
     * @return string
     */
    protected function _getHtmlInterpreterDesktopBody()
    {
        $sId=$this->getId();
        $sControlId=$this->getProperty('System/Standard/Control id')->getValue();
        if($this->hasContainer())
        {
            $sId = $sId."_0";
            $sControlId = $sControlId."_0";
        }


        return '<div 
        style="
            position:absolute; 
            left:0px; 
            top:0px; 
            right:0px; 
            bottom:0px; 
            '.($this->hasContainer()?'border:1px solid gray; ':'').'
            "
        '.$this-> _getHtmlInterpreterDeskopContainerAttribute($sId, $sControlId).'
        >'.$this->_getHtmlInterpreterDesktopName($sId).'</div>';
    }


    /**
     * @return string
     */
    protected function _getHtmlInterpreterDesktopFooter()
    {
        return '</div>';
    }


    /**
     * @param $sId
     * @param $sTitle
     * @return string
     */
    protected function _getHtmlInterpreterDesktopName($sId)
    {
        return '<!--CONTENT_'.$sId.'-->';
    }


    /**
     * @return string
     */
    public function getCssInterpreter()
    {
        $path = explode('\\', $this->getClassName());
        $sClass = array_pop($path);

        //@import url('src/css/tooltip.css');
        $sFile = "formedit/addons/controls/".$sClass."/interpreter.css";
        $sPath = \formedit\inc\config::getInstance()->getSystemPathReal()."/".$sFile;

        if(file_exists($sPath))
        {
            return "@import url('".$sFile."');".PHP_EOL;
        }
    }


    /**
     * @return string
     */
    public function getJsInterpreter()
    {
        $path = explode('\\', $this->getClassName());
        $sClass = array_pop($path);

        //@import url('src/css/tooltip.css');
        $sFile = "formedit/addons/controls/".$sClass."/interpreter.js";
        $sPath = \formedit\inc\config::getInstance()->getSystemPathReal()."/".$sFile;

        if(file_exists($sPath))
        {
            return '<script src="'.$sFile.'"></script>'.PHP_EOL;
        }
    }



#endregion

}