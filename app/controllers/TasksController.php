<?php 
use Task\Task;
use Task\TasksRepository;

class TasksController extends \BaseController {

	private $repository;

	public function __construct(TasksRepository $repository) 
	{
		$this->repository = $repository;
		$this->repository->setListener($this);
	}

	// ------------------------------------------------------------------------
	public function index()
	{
		return View::make('site.tasks.index', ['tasks'=>Task::unClaimed()->get(), 'claimed_tasks'=>Task::claimed()->get()]);	
	}
	
	// ------------------------------------------------------------------------
	public function show($id)
	{
	
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