<?php $categories = Category::all(); $count = 0; ?>
<?php 
$old_categories = [];
if(isset($spot)) {
	foreach ($spot->categories as $c) {
		array_push($old_categories, $c->id);
	}
}
else {
	$old_categories = Input::old('category', []); 
}

$categories_chunks = array_chunk($categories->toArray(), (count($categories)/2)+1);
?>

<div class="row">
@foreach ($categories_chunks as $categories)
	<div class="col-sm-6">
	@foreach ($categories as $category)
		<?php $category = (object)$category ?>
		<div class="checkbox">
			<label>
				<input 	type="checkbox" 
						id="category-{{$category->id}}" 
						value="{{$category->id}}" 
						name="category[]" 
						{{in_array($category->id,$old_categories)?'checked':''}}>
				
				{{$category->name}} 
			</label>	
			<span 	class="category-info glyphicon glyphicon-question-sign"
					data-toggle="modal" 
					data-target=".category-modal"
					href="{{URL::to('api/category/'.$category->id.'?html=true')}}">
			</span> 
			   

			
		</div>
	@endforeach
	</div>

@endforeach
</div>