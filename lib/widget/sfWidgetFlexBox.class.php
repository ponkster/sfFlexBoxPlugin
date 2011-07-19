<?php

/**
 * Description of sfWidgetFlexBoxclass
 * class to define and implement FlexBox widget
 * @author ponkster
 */
class sfWidgetFlexBox extends sfWidgetForm {
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * image:       The image path to represent the widget (false by default)
   *  * config:      A JavaScript array that configures the JQuery date widget
   *  * culture:     The user culture
   *  * date_widget: The date widget instance to use as a "base" class
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
    public function configure($options=array(), $attributes = array())
    {

     $results =is_array($options['results']) ? (count($options['results']) > 0) ? $options['results'] : array() : array();

     $this->addOption('isDependable',false);
         $this->addOption('template.javascript',
      '<script type="text/javascript">
        var {dataset} = {};
        {dataset}.results = {results};
        jQuery("#{div.id}").flexbox({dataset});
      </script>');
  if(array_key_exists('isDependable', $options))
     {
     if($options['isDependable']==true)
         {
         $this->addRequiredOption('url');
         $this->addRequiredOption('parentName');
         $this->addOption('template.javascript', '
         <script type="text/javascript">
         jQuery("#{div.id}").flexbox("{url}");
         jQuery("#{parent_id}_input").bind("blur", function() {
         });
         </script>
         ');
     }

     }
     

     
     $this->addOption('results',$results);
     $this->addOption('template.html', '
      <div id="{div.id}"></div>
    ');

        
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

     $isDependable = $this->getOption('isDependable');

    if($isDependable){
              $template_vars = array(
              '{parent_id}'        => $this->generateId($this->getOption('parentName')),
              '{div.id}'        => $this->generateId($name),
              '{url}'          => $this->getOption('url')
            );
    }
    else{
               $template_vars = array(
              '{div.id}'        => $this->generateId($name),
              '{results}'          => json_encode($this->createDataset($this->getOption('results'))),
              '{dataset}'       => $this->generateId($name."[dataset]"),
            );
    }



      return strtr(
      $this->getOption('template.html').$this->getOption('template.javascript'),
      $template_vars
    );


    }



}
?>
