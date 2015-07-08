<?php $categories = Category::all(); $count = 0; ?>
@for($i = 0; $i<sizeof($categories); $i++)
	<?php $category = $categories[$i]; ?>
	<div class="col-sm-4">
		<div class="checkbox">
			<label>
				<input 	type="checkbox" 
						id="category-{{$category->id}}" 
						value="{{$category->id}}" 
						name="category[]" 
						{{(in_array($category->id, $old_categories))?'checked':''}} >
					{{$category->name}}
			</label>   
    	</div>
	</div>	
@endfor