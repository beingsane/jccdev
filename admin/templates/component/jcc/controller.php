<?php
use \JCC\Controller\BaseController;

defined("_JEXEC") or die("Restricted access");

class BooksController extends  \JCC\Controller\BaseController {

  protected $component = 'Books';
  protected $default_view = '##Plural##';
  protected $routes = [
      [ 
        'pattern' => '##Plural##/:id',
        'action' => '##Singular##',
        'rules' => [
          'id' => '(\d+)'
        ]
      ],
      [ 
        'pattern' => '##Plural##',
        'action' => '##Plural##'
      ]
  ];

  function action_##Singular##($vars) {
    $this->model = $this->getModel('##Singular##');
     if (!isset($vars['id'])) action_##Plural##($vars);
     else {
       echo $this->twig(JPATH_COMPONENT_SITE.'/twig')
         ->render('view.html.twig', array(
                    '##Singular##' => $this->model->getItem($vars['id'])
        ));
     }
  }

  function action_##Plural##($vars) {
    $this->model = $this->getModel('##Plural##');    
    echo $this->twig(JPATH_COMPONENT_SITE.'/twig')
       ->render('list.html.twig', array(
        '##Plural##' => $this->model->getItems(),
        'navigation' => '',
        ));
  }

}
?>