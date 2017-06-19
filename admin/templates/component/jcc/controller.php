<?php##{start_header}##
/**
 * @package     JCCDev
 * @subpackage  Templates.Component
 *
 * @copyright...
 * @license     GNU General Public License version 2 or later
 */

##{end_header}##

use \JCC\Controller\BaseController;

class ##Component##Controller extends  \JCC\Controller\BaseController {

  protected $component = '##Component##';
  protected $default_view = '##component##';
  protected $routes = [
      [ 
        'pattern' => '##plural##/:id',
        'action' => 'info',
        'rules' => [
          'id' => '(\d+)'
        ]
      ],
      [ 
        'pattern' => '##plural##',
        'action' => '##plural##'
      ]
  ];

  function action_info($vars) {
    $this->model = $this->getModel('info');
     if (!isset($vars['id'])) action_##plural##($vars);
     else {
       echo $this->twig(JPATH_COMPONENT_SITE.'/twig')
         ->render('view.twig', array(
                    '##singular##' => $this->model->getItem($vars['id'])
        ));
     }
  }

  function action_##plural##($vars) {
    $this->model = $this->getModel('list');    
    echo $this->twig(JPATH_COMPONENT_SITE.'/twig')
       ->render('list.twig', array(
        '##plural##' => $this->model->getItems(),
        'navigation' => '',
        ));
  }

}
?>