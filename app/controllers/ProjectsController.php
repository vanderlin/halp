<?php 


class ProjectsController extends \BaseController {

	private $repository;

	public function __construct(Project\ProjectsRepository $repository) 
	{
		$this->repository = $repository;
		$this->repository->setListener($this);
	}

	// ------------------------------------------------------------------------
	public function index()
	{	
		return Redirect::to('/');
	}
	
	// ------------------------------------------------------------------------
	public function show($id)
	{

		$project = Project\Project::find($id);
		$title = $project ? link_to($project->getURL(), $project->title) : NULL;

		Paginator::setPageName('tasks_page');
		$tasks = Task\Task::unClaimed()->whereHas('Project', function($q) use($id) {
			$q->where('id', '=', $id);
		})->paginate(16);

		Paginator::setPageName('claimed_tasks_page');
		$claimed_tasks = Task\Task::claimed()->whereHas('Project', function($q) use($id) {
			$q->where('id', '=', $id);
		})->paginate(8);

		return View::make('site.tasks.index', ['tasks'=>$tasks, 'title'=>$title, 'claimed_tasks'=>$claimed_tasks]);	

	}
	
	// ------------------------------------------------------------------------
	public function search()
	{
		$q = Input::get('q');
		return Project\Project::where('title', 'LIKE', "%$q%")->get();	
	}

	// ------------------------------------------------------------------------
	public function create($id)
	{
	
	}

	// ------------------------------------------------------------------------
	public function store() 
	{
		return $this->repository->store(Input::all());
	}

	// ------------------------------------------------------------------------
	public function update($id) 
	{
		return $this->repository->update($id);
	}

	// ------------------------------------------------------------------------
	public function delete($id) 
	{
		return $this->repository->delete($id);
	}
}