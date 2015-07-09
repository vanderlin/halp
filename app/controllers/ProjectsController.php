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
		return Project\Project::all();
	}
	
	// ------------------------------------------------------------------------
	public function show($id)
	{
	
	}
	
	// ------------------------------------------------------------------------
	public function search()
	{
		$q = Input::get('q');
		return Project\Project::where('title', 'LIKE', $q)->get();	
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