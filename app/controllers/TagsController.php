<?php

class TagsController extends \BaseController {

	
	public function store() {
		$input 	   = Input::all();
		$wantsJson = Input::wantsJson();
		$rules = array(
			'name'=> 'required|unique:tags,name',
			'user_id'=>'required|exists:users,id',
		);
		$messages = array(
			'name.unique'=>'The tag has already been created.'
		);
		
		$validate = Validator::make($input, $rules, $messages);
		
		// -----------------------------
		// error with / data
		// -----------------------------
		// dd($validate->getMessageBag()->toArray());
		if($validate->fails()) {
			return $this->statusResponse(['errors'=>$validate->getMessageBag()->toArray(), 'input'=>$input], 400);	
		}
		
		$tag = new Tag;
		$tag->name = Input::get('name');
		$tag->slug = Str::slug(strtolower($tag->name));
		$tag->user()->associate(User::find(Input::get('user_id')));

		$tag->save();


		return $this->statusResponse(['notice'=>'Tag Created', 'tag'=>$tag], 200, false);	
	}

	public function update($id) {
		$tag = Tag::find($id);

		if($tag == null) {
			return $this->statusResponse(['error'=>'No Tag found']);
		}

		if(Input::has('name')) {
			$tag->name = Input::get('name');
			$tag->slug = Str::slug(strtolower($tag->name));
		}

		$tag->save();

		return $this->statusResponse(['notice'=>'Tag Updated', 'tag'=>$tag]);	
	}

	public function destroy($id) {

		$tag = Tag::find($id);
		$tag->delete();

		if($tag == null) {
			return $this->statusResponse(['error'=>'No Tag found']);
		}

		return $this->statusResponse(['notice'=>'Tag deleted', 'tag'=>$tag]);

	
	}

}