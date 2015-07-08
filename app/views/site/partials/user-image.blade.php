<?php $size = isset($size) ? $size : 's112'; ?>
<?php $img_size = Asset::parseSize($size);  ?>
<a  href="{{$user->getProfileURL()}}"
	class="user-image"
	remove-on-hide="false" 
	data-id="{{$user->id}}"
	data-office-slug="{{$user->office->slug}}"
	data-location="{{$user->location->name.', '.$user->location->location->shortState}}"
	data-name="{{$user->getName()}}">
	<img data-pin-no-hover="true" alt="{{$user->getName()}}" style="border-width:{{min(7, $img_size->width*0.08)}}px" width="{{$img_size->width}}" height="{{$img_size->height}}" src="{{ $user->profileImage->url($size) }}" class="{{$user->isSpotter()?'img-spotter':''}} {{$user->isEditor()?'editor':''}}  img-circle {{isset($class)?$class:''}}">
</a>


