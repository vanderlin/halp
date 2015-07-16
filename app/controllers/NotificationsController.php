<?php 

use Notification\Notification;
use Notification\NotificationsRepository;

class NotificationsController extends \BaseController {

	private $repository;

	public function __construct(NotificationsRepository $repository) 
	{
		$this->repository = $repository;
		$this->repository->setListener($this);
	}

	// ------------------------------------------------------------------------
	public function index()
	{
		
	}

	// ------------------------------------------------------------------------
	public function send($id)
	{
		$notice = $this->repository->get($id);
		$send_status = $notice->send();

		return $this->statusResponse(['notice'=>$notice, 'send_status'=>$send_status]);
	}
	
}