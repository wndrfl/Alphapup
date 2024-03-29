<?php
namespace Profiler\Application\Controller;

use Alphapup\Core\Controller\Controller;

class Profile extends Controller
{	
	public function index($id)
	{
		$this->disableProfiler();
		$view = $this->get('view');
		$profiler = $this->get('profiler');
		$profile = $profiler->getProfile($id);
		$view->profile = $profile;
		
		$view->theme('Profiler','Application/Theme/Default');
		$view->addView('Profiler','Application/View/Profiler/Profile/Index.php');
		$view->display();
	}
	
	public function config($id)
	{
		$this->disableProfiler();
		$view = $this->get('view');
		$profiler = $this->get('profiler');
		$profile = $profiler->getProfile($id);
		$view->profile = $profile;
		
		$view->theme('Profiler','Application/Theme/Default');
		$view->addView('Profiler','Application/View/Profiler/Profile/Config.php');
		$view->display();
	}
	
	public function dexter($id)
	{
		$this->disableProfiler();
		$view = $this->get('view');
		$profiler = $this->get('profiler');
		$profile = $profiler->getProfile($id);
		
		$view->totalQueries = count($profile->collector('dexter')->queries());
		
		$totalQueryTime = 0;
		foreach($profile->collector('dexter')->queries() as $query) {
			$totalQueryTime += $query['totalTime'];
		}
		$view->totalQueryTime = $totalQueryTime;
		
		$view->profile = $profile;
		$view->theme('Profiler','Application/Theme/Default');
		$view->addView('Profiler','Application/View/Profiler/Profile/Dexter.php');
		$view->display();
	}

	public function events($id)
	{
		$this->disableProfiler();
		$view = $this->get('view');
		$profiler = $this->get('profiler');
		$profile = $profiler->getProfile($id);
		$view->profile = $profile;
		
		$view->theme('Profiler','Application/Theme/Default');
		$view->addView('Profiler','Application/View/Profiler/Profile/Events.php');
		$view->display();
	}
}