<?php

namespace Privalia\SQHeal\Controller;

use Privalia\SQHeal\Model\SonarQube\API\Issue;

class Home extends Base {

    protected function __construct(\Silex\Application $app){

        $this->app = $app;
        $this->controller = $app['controllers_factory'];

        $this->controller->get('/'              , [$this, 'main'])
            ->bind('main');

        $this->controller->get('/hall-of-fame'  , [$this, 'hallOfFame'])
            ->bind('hall-of-fame');

        $this->controller->get('/dashboard'     , [$this, 'dashboard'])
            ->bind('dashboard');

        //@TODO move API calls to API controller
        $this->controller->get('/api/issue.count.global.json' , [$this, 'issue_count_global_json'])
            ->bind('issue.count.global.json');

        $this->controller->get('/api/issue.count.delta.json'  , [$this, 'issue_count_delta_json'])
            ->bind('issue.count.delta.json');
    }

    protected $app = null;

    public function main(){
        return $this->app['twig']->render('main.twig');
    }

    public function dashboard(){
        return $this->app['twig']->render('dashboard.twig');
    }

    public function hallOfFame(){
        return 'Hall of fame';
    }

    public function issue_count_delta_json(){

        $issueModel = new Issue($this->app['config']['app']['sonarqube']);
        $resourceID = $this->app['config']['app']['sonarqube']['resourceID'];

        $result = $issueModel->getViolationSolvingByDateRange(
            $resourceID,
            new \DateTime('last monday'),
            new \DateTime('next monday')
            );
        return $this->app->json($result);
    }

    public function issue_count_global_json(){

        $issueModel = new Issue($this->app['config']['app']['sonarqube']);
        $resourceID = $this->app['config']['app']['sonarqube']['resourceID'];

        $result = $issueModel->getViolationCountBySeverity($resourceID);
        return $this->app->json($result);
    }

}


