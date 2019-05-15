<?php
namespace formedit\addons\connections;

class mysql extends \formedit\core\interfaces\connection
{
    /**
     * @var \mysqli
     */
    protected $_oDb=null;

    public function __sleep()
    {
        // TODO: Implement __sleep() method.
        if($this->_oDb!==null)
        {
            unset($this->_oDb);
            $this->_oDb=null;
        }
    }

    /**
     * @return \mysqli|null
     */
    protected function _getConnection()
    {
        if($this->_oDb===null)
        {
            $this->_oDb = new \mysqli($this->getData('host'),$this->getData('user'),$this->getData('pass'), $this->getData('schema'), $this->getData('port'));
            $this->_oDb->query("SET NAMES '".$this->getData('charset')."'");
        }
        return $this->_oDb;
    }


    /**
     * @param \mysqli_result|null $oResult
     * @return array|mixed|null
     */
    protected function _convertResultToJson(\mysqli_result $oResult=null)
    {
        $aResult=null;
        if($oResult!==null)
        {
            $aColumns=[];
            while ($oColumn = $oResult->fetch_field()) {
                $aC=[];
                $aC['name']=$oColumn->name;
                $aC['table']=$oColumn->table;
                $aC['maxLen']=$oColumn->max_length;
                $aC['flags']=$oColumn->flags;
                $aC['type']=$oColumn->type;
                $aColumns[]=$aC;
            }

            $aData=[];
            while ( $oRow = $oResult->fetch_assoc())  {
                $aData[]=$oRow;
            }

            $aResult=[];
            $aResult['columns']=$aColumns;
            $aResult['data']=$aData;

            $aResult = json_decode(json_encode($aResult));
        }
        return $aResult;
    }

    public function _generateSql($sSql, $sWhere="", $sHaving="", $sOrderBy="", $sLimit="", $aParam=[])
    {
        if($sWhere!="")
        {
            if(substr(trim(strtolower($sWhere)),0,strlen('where '))!="where ")
                $sWhere="WHERE ".$sWhere;
        }
        if($sHaving!="")
        {
            if(substr(trim(strtolower($sHaving)),0,strlen('having '))!="having ")
                $sHaving="HAVING ".$sHaving;
        }
        if($sOrderBy!="")
        {
            if(substr(trim(strtolower($sOrderBy)),0,strlen('order by '))!="order by ")
                $sOrderBy="ORDER BY ".$sOrderBy;
        }
        if($sLimit!="")
        {
            if(substr(trim(strtolower($sLimit)),0,strlen('limit '))!="limit ")
                $sLimit="LIMIT ".$sLimit;
        }
        $sSql=str_replace("#WHERE#"," ".$sWhere." ", $sSql);
        $sSql=str_replace("#HAVING#"," ".$sHaving." ", $sSql);
        $sSql=str_replace("#ORDERBY#"," ".$sOrderBy." ", $sSql);
        $sSql=str_replace("#LIMIT#"," ".$sLimit." ", $sSql);
        foreach($aParam as $sKey => $sValue)
        {
            $sSql=str_replace("#".$sKey."#",$sValue, $sSql);
        }

        // $sSql."<br>";
        return $sSql;
    }

    /**
     * @return null|object
     */
    public function getObjects():?object
    {
        return $this->execute("show full tables");
    }
    public function getObjectId(object $oRow):string
    {
        $sId = "";
        foreach($oRow as $sColumnName => $sValue)
        {
            $sId = $sValue;
            break;
        }
        return $sId;
    }
    public function getObjectName(object $oRow):string
    {
        $sName = "";
        $aAttributes = [];
        foreach($oRow as $sColumnName => $sValue)
        {
            if($sName=="")
                $sName = $sValue;
            else
            {
                $aAttributes[]=$sColumnName."=".$sValue;
            }
        }
        return $sName.(count($aAttributes)>0?" (".implode(", ",$aAttributes).")":"");
    }

    /**
     * @param string $sObject
     * @return null|object
     */
    public function getObjectMembers(string $sObject):?object
    {
        return $this->execute("desc `$sObject`");
    }
    public function getObjectMemberId(object $oRow):string
    {
        $sId = "";
        foreach($oRow as $sColumnName => $sValue)
        {
            $sId = $sValue;
            break;
        }
        return $sId;
    }
    public function getObjectMemberName(object $oRow):string
    {
        $sName = "";
        $aAttributes = [];
        foreach($oRow as $sColumnName => $sValue)
        {
            if($sName=="")
                $sName = $sValue;
            else
            {
                if($sValue!="")
                    $aAttributes[]=$sColumnName."=".$sValue;
            }
        }
        return $sName;
    }
    public function getObjectMemberAttributes(object $oRow):string
    {
        $sName = "";
        $aAttributes = [];
        foreach($oRow as $sColumnName => $sValue)
        {
            if($sName=="")
                $sName = $sValue;
            else
            {
                if($sValue!="")
                    $aAttributes[]=$sColumnName."=".$sValue;
            }
        }
        return implode(", ",$aAttributes);
    }

    /**
     * @return string
     */
    public function getDatasetPopupHtml():string
    {
        return "<div id='mysql_dataset_popup'>
            <div class='property_popup_content_title'>SQL to select the content. (#ORDERBY#, #WHERE#, #HAVING#, #LIMIT#) (Tipp: SQL_CALC_FOUND_ROWS)</div>
            <div class='property_popup_content_section'>
                <textarea data-name='sql_content' style='display:none; '></textarea>
                <pre data-mode='mysql'></pre>
            </div>
            <div class='property_popup_content_title'>SQL to return the count of all rows. SQL must return only the number (#WHERE#, #HAVING#) (Tipp: SELECT FOUND_ROWS())</div>
            <div class='property_popup_content_section'>
                <textarea data-name='sql_count' style='display:none; '></textarea>
                <pre data-mode='mysql'></pre>    
            </div>
            <div class='property_popup_content_title'>SQL WHERE which represent the where part of an sql statment. This will use in the #WHERE#-variable.</div>
            <div class='property_popup_content_section'>
                <textarea data-name='sql_where' style='display:none; '></textarea>
                <pre data-mode='mysql'></pre>
            </div>
            <div class='property_popup_content_title'>SQL WHERE which represent the having part of an sql statment. This will use in the #HAVING#-variable.</div>
            <div class='property_popup_content_section'>
                <textarea data-name='sql_having' style='display:none; '></textarea>
                <pre data-mode='mysql'></pre>
            </div>
        </div>
        <script>
        var aEditorDataset=[];
        $( document ).on('property_popup_open', function() {
            $(this).find('div#mysql_dataset_popup pre').each(function() {
                var oTextarea = $(this).prev();
                var mode = $(this).attr('data-mode');
                var e = ace.edit($(this)[0]);
                e.setTheme('ace/theme/twilight');
                e.session.setMode({path:'ace/mode/' + mode.toLowerCase(), inline: true});
                e.setValue(oTextarea.val());
                e.on('blur', function(event) {
                    let value = e.getValue();
                    oTextarea.val(value);
                });
                aEditorDataset.push(e);
                
            });
        });
        $( document ).on('property_popup_close', function() {
            aEditorDataset.forEach(function(element) {
                element.destroy();
            });
        });
        </script>
        ";
    }

    public function getDatasetFilterPopupHtml():string
    {
        $sHtml="<div id='mysql_dataset_filter_popup'>
        
        <table class='mysql_dataset_filter_popup_content' border='1' cellspacing='0' cellpadding='2'>
            <thead style='position:sticky; top:-10px; '>
                <tr>
                    <th>Used for</th>
                    <th>Display</th>
                    <th>Type</th>
                    <th>Filter (Variable: #VALUE#)</th>
                </tr>
            </thead>
            <tbody>";
                for($x=0;$x<20;$x++)
                {
                    $sHtml.="<tr>
                        <th colspan='4'>
                            <button class='show' type='button'>+</button>
                            <button class='hide' type='button' style='display:none; '>-</button>
                        </th>
                    </tr>
                    <tr style='display:none; '>
                        <td>
                            <select data-name='usefor[$x]'>
                                <option value='where'>WHERE</option>
                                <option value='having'>HAVING</option>
                            </select>
                        </td>
                        <td><input type='text' data-name='display[$x]' value=''></td>
                        <td>
                            <select data-name='type[$x]'>
                                <option value=''>Text</option>
                                <option value='date'>Date (YYYY-MM-DD)</option>
                                <option value='datetime'>Datetime (YYYY-MM-DD HH:MM:SS)</option>
                                <option value='boolean'>Boolean (1,0)</option>
                            </select>
                        </td>
                        <td><textarea data-name='filter[$x]'></textarea></td>
                    </tr>";
                }
            $sHtml.="</tbody>
        </table>
        </div>
        <script>
        $('#mysql_dataset_filter_popup button.show').click(function() {
            $(this).css('display','none');
            $(this).parent().find('.hide').css('display','inline');
            $(this).parent().parent().next().css('display','table-row');
        });
        $('#mysql_dataset_filter_popup button.hide').click(function() {
            $(this).css('display','none');
            $(this).parent().find('.show').css('display','inline');
            $(this).parent().parent().next().css('display','none');
        });

        $( document ).on('property_popup_open', function() {
            $(this).find('div#mysql_dataset_filter_popup input').each(function() {
                if($(this).val()=='')
                {
                    $(this).parent().parent().prev().find('button.hide').click();
                }
                else 
                {
                    $(this).parent().parent().prev().find('button.show').click();
                }
            });
        });

        </script>";

        return $sHtml;
    }

    public function getDatasetMembers(object $oData):?array
    {
        $sLimit = "limit 0";
        $sSql=$this->_generateSql($oData->sql_content, $oData->sql_where, $oData->sql_having,"",$sLimit);
        if($oJson = $this->execute($sSql))
        {
            return $oJson->columns;
        }
        return null;
    }



    /**
     * @param \core\form $oForm
     * @param string $sId
     */
    public function loadValues($oForm, $sId)
    {

    }

    /**
     * @param \core\form $oForm
     * @param array $aData
     */
    public function saveValues($oForm, $aData)
    {

    }

    /**
     * @param \core\form $oForm
     * @param string $sId
     */
    public function delete($oForm, $sId)
    {

    }

    /**
     * @param $sSql
     * @return object
     */
    public function execute(string $sSql):?object
    {
        $oJson = null;
        if($oDb = $this->_getConnection())
        {
            $oRs = $oDb->query($sSql);
            $oJson = $this->_convertResultToJson($oRs);
            if($oRs!==null)
                $oRs->close();
        }
        return $oJson;
    }

    public function getResult(object $oData):?object
    {
        $sSql=$this->_generateSql($oData->sql_content,$oData->sql_where,$oData->sql_having,$oData->sql_orderby,$oData->sql_limit);
        return $this->execute($sSql);
    }

    public function getResultCount(object $oData):?int
    {
        $sSql=$this->_generateSql($oData->sql_content,$oData->sql_where,$oData->sql_having,$oData->sql_orderby,$oData->sql_limit);
        $oValue = $this->getOne($sSql);
        if(!is_null($oValue))
            $oValue = (int) $oValue;
        return $oValue;
    }

    /**
     * @param string $sSql
     * @return object
     */
    public function getOne($sSql)
    {
        $oValue=null;
        if($oDb = $this->_getConnection())
        {
            if($oRs = $oDb->query($sSql))
            {
                $oRow = $oRs->fetch_row();
                $oValue = reset($oRow);
            }
            if($oRs!==null)
                $oRs->close();
        }
        return $oValue;
    }
}