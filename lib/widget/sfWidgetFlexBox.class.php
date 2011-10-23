<?php

/**
 * Description of sfWidgetFlexBoxclass
 * class to define and implement FlexBox widget
 * @author ponkster
 */
class sfWidgetFlexBox extends sfWidgetForm {
    
    protected $base_settings = array(
        "method" => "GET", // One of "GET" or "POST"
        "queryDelay" => 100, // num of milliseconds before query is run.
        "allowInput" => "true", // set to false to disallow the user from typing in queries
        "containerClass" => "ffb",
        "contentClass" => "content",
        "selectClass" => "ffb-sel",
        "inputClass" => "ffb-input",
        "arrowClass" => "ffb-arrow",
        "matchClass" => "ffb-match",
        "noResultsText" => "No matching results", // text to show when no results match the query
        "noResultsClass" => "ffb-no-results", // class to apply to noResultsText
        "showResults" => "true", // whether to show results at all, or just typeahead
        "selectFirstMatch" => "true", // whether to highlight the first matching value
        "autoCompleteFirstMatch" => "false", // whether to complete the first matching value in the input box
        "highlightMatches" => "true", // whether all matches within the string should be highlighted with matchClass
        "highlightMatchesRegExModifier" => "i", // "i" for case-insensitive, "g" for global (all occurrences), or combine
	"matchAny" => "true", // for client-side filtering ONLY, match any occurrence of the search term in the result (e.g. "ar" would find "area" and "cart")
        "minChars" => 1, // the minimum number of characters the user must enter before a search is executed
        "showArrow" => "true", // set to false to simulate google suggest
        "arrowQuery" => "", // the query to run when the arrow is clicked
        "onSelect" => "false", // function to run when a result is selected
        "maxCacheBytes" => 32768, // in bytes, 0 means caching is disabled
        "resultTemplate" => "{name}", // html template for each row (put json properties in curly braces)
        "displayValue" => "name", // json element whose value is displayed on select
        "hiddenValue" => "id", // json element whose value is submitted when form is submitted
        "initialValue" => "", // what should the value of the input field be when the form is loaded?
        "watermark" => "", // text that appears when flexbox is loaded, if no initialValue is specified.  style with css class ".ffb-input.watermark"
        "width" => 200, // total width of flexbox.  auto-adjusts based on showArrow value
        "resultsProperty" => "results", // json property in response that references array of results
        "totalProperty" => "total", // json property in response that references the total results (for paging)
        "parentObject" => null,
        "selectBehavior" => "false",
        "maxVisibleRows" => 0, // default is 0, which means it is ignored.  use either this, or paging.pageSize
        "paging" => array(
            "style" => "input", // or "links"
            "cssClass" => "paging", // prefix with containerClass (e.g. .ffb .paging)
            "pageSize" => 10, // acts as a threshold.  if <= pageSize results, paging doesn"t appear
            "maxPageLinks" => 5, // used only if style is "links"
            "showSummary" => "true", // whether to show "displaying 1-10 of 200 results" text
            "summaryClass" => "summary", // class for "displaying 1-10 of 200 results", prefix with containerClass
            "summaryTemplate" => "Displaying {start}-{end} of {total} results" // can use {page} and {pages} as well
        )
    );

 

    public function configure($options=array(), $attributes = array())
    {
        $this->addRequiredOption("results");

        foreach ($this->base_settings as $k => $v){
            $this->addOption($k, $v);
        }
    }
  public function getStylesheets()
  {
      return array(
        '/sfFlexBoxPlugin/css/jquery.flexbox.css',
      );
  }

  protected function createDataset($results)
  {
     $datas = array();
     foreach($results as $k => $v)
     {
         $data = array("id" => $k, "name" => $v);
         array_push($datas,$data);

     }

     return $datas;
  }
  public function getJavaScripts()
  {
    return array(
      '/sfFlexBoxPlugin/js/jquery.flexbox.js',
      '/sfFlexBoxPlugin/js/largedataset.js',

    );
  }

    public function render($name, $value = null, $attributes = array(), $errors = array())
    {
        /*
         * intersect the options keys with base_settings keys to get the new clean base_settings
         */
        
        $new_base_settings = array_intersect_key($this->getOptions(), $this->base_settings);
   

        /*
         * get the clean settings from new_base_settings to get new settings (base upon configured options
         */
        $settings = array_diff_assoc($new_base_settings, $this->base_settings);
        
        $s="";
        
          foreach($settings as $key => $value){
             if($key=='parentObject' && $value!=null){
              $s .= sprintf("%s: jQuery(\"#%s\").get(0), \n", $key, $this->generateId($value)."_hidden");     
             } 
             elseif($key=='paging' && is_array($value)){
                 $s .= sprintf("%s: %s, \n", $key, json_encode($value));
             }
             elseif(substr($key,0,2)=='on'){
                $s .= sprintf("%s: %s, \n", $key, $value);
            }
             else
            $s .= sprintf("%s: \"%s\", \n", $key, $value);
          }

          $results= is_array($this->getOption("results")) ? $this->createDataset($this->getOption("results")) : $this->getOption("results");
          
          if(count($settings)==0){
              $s=sprintf("%s",$results); //no need advanced options
              $flexBox=sprintf("<div id=\"%s\"></div> \n
                  <script type=\"text/javascript\"> \n
                  var myDatas= {};
                  myDatas.results=%s;
                  jQuery(\"#%s\").flexbox(myDatas);</script>",$this->generateId($name),json_encode($results),$this->generateId($name));
          }
        else{
            if(is_array($results)){
              $flexBox=sprintf("<div id=\"%s\"></div> \n
                  <script type=\"text/javascript\"> \n
                  var myDatas={};
                  myDatas.results=%s;
                  jQuery(\"#%s\").flexbox(myDatas,{\n
                  %s}); \n</script>",$this->generateId($name),json_encode($results),$this->generateId($name),$s);
                
            }
            else{
              $flexBox=sprintf("<div id=\"%s\"></div> \n
                  <script type=\"text/javascript\"> \n
                  jQuery(\"#%s\").flexbox(\"%s\", {\n
                  %s}); \n</script>",$this->generateId($name),$this->generateId($name),$results,$s);
            }
        }
        
        return $flexBox;
    }



}
?>
