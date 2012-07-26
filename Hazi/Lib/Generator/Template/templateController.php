<?php

namespace Hazi\Lib\Generator\Template;

//require_once ("template.php");

//use Hazi\Lib\Template;
use Symfony\Component\HttpFoundation\Response;

class templateController extends template
{
  /* La app en si misma. Necesaria al basarse en silex. En 
    * $this->app['session']->get('schema')
    *estan las columnas de la tabla elegida 
  */
  protected $app;

  /* Nombre de la aplicación */
  protected $app_name;

  /* Tabla sobre la que se quieren generar cosas */
  protected $table;

  //protected $controller;

  public function __construct($data = array()) {
    $this->app = $data["app"];
    $this->app_name = $data["app_name"];
    $this->table = $data["table"];

    parent::__construct($data);

    if ($this->_openController()) { 
    echo "HOLA";
    } else {
      echo "ERROR al crear el fichero";
    }
  }

   /**
   * template. create
   *
   *
   * @access public
   * @since 0.0
   *
   *
   * @return mixed
   */

   public function _openController()
   {
    $file = __DIR__."/../../../../web/index.php";
    $controller = fopen($file, 'r') or die("can't open file");
    $read_file = fread($controller, filesize($file));
    $read_file = substr($read_file, 0, -13);
    fclose($controller);

    $data = $read_file."\n";
    $data .= "\n\$app->match('/".$this->table."/add', function () use (\$app){
     \$data = null;
   \$".$this->app_name."_model = 
      new ".ucfirst($this->app_name)."\Model\ ".$this->app_name."_model(\$app);
var_dump(\$_POST);
      if (\$_POST){
       if (\$".$this->app_name."_model->add".ucfirst($this->table)."()) {
          echo 'GUARDADO';
       } else {
          echo 'ERROR!';
       }
      } else {
        require_once __DIR__.'/../generator/gozatzen/view/links/add.php';
      }
})
->method('GET|POST');

\$app->match('/".$this->table."/edit/{id}', function (\$id) use (\$app){
   \$data = null;
   \$".$this->app_name."_model = 
      new ".ucfirst($this->app_name)."\Model\ ".$this->app_name."_model(\$app);
     if (\$_POST){
       if (\$".$this->app_name."_model->edit".ucfirst($this->table)."(\$id)) {
          echo 'GUARDADO';
       } else {
          echo 'ERROR!';
       }
      } else {
        \$data = \$gozatzen_model->getById(\$id);
        require_once __DIR__.'/../generator/gozatzen/view/links/edit.php';
      }
})
->method('GET|POST');

\$app->match('/".$this->table."/delete/{id}', function (\$id) use (\$app){
  \$".$this->app_name."_model = 
      new ".ucfirst($this->app_name)."\Model\ ".$this->app_name."_model(\$app);
   if (\$".$this->app_name."_model->delete".ucfirst($this->table)."(array('id' => \$id))) {
     echo 'BORRADO';
   } else {
      echo 'ERROR!';
   }
})
->method('GET|POST');


\$app->match('/".$this->table."/list/{desde}', function (\$desde) use (\$app){
   \$cuantos = 10;
  \$".$this->app_name."_model = 
      new ".ucfirst($this->app_name)."\Model\ ".$this->app_name."_model(\$app);
   if (\$data = \$".$this->app_name."_model->get".ucfirst($this->table)."()) {
     
     \$data = \$".$this->app_name."_model->paginator".ucfirst($this->table)."(\$desde, \$cuantos);
    \$paginator['total'] = \$".$this->app_name."_model->count".ucfirst($this->table)."();
    \$paginator['paginator'] = round(\$paginator['total'][0]['howMany'] / \$cuantos);
     require_once __DIR__.'/../generator/gozatzen/view/links/list.php';
   } else {
      echo 'ERROR!';
   }
})
->method('GET');\n\n";
    $data .= "\$app->run();";
    $controller = fopen($file, 'w') or die("can't open file");
    fwrite($controller, $data);

    fclose($controller);

    return true;
   }

  /**
   * template.fill
   *
   *
   * @access public
   * @since 0.0
   *
   *
   * @return mixed
   */

    public function _fill() 
    {
     
return new Response('html generado!');
    }
}