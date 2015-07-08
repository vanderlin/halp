<?php

class PermissionsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /permissions
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /permissions/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /permissions
	 *
	 * @return Response
	 */
	public function store() {
		$permission = new Permission;
		$permission->name = Input::get('name');
		$permission->display_name = Input::get('display_name');
		if($permission->save()) {
		    return Redirect::back()->with('permissions-notice', '"'.Input::get('display_name').'" has been created.');
        } 
        else {
            return Redirect::back()->with('permissions-errors', $permission->errors());
        }
	}

	/**
	 * Display the specified resource.
	 * GET /permissions/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /permissions/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /permissions/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /permissions/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}