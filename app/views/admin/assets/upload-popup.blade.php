<div class="white-popup claimed-popup animated fadeIn">
    <div class="popup-content">

        {{Form::open(['route'=>'assets.store', 'method'=>'POST', 'files'=>true, 'class'=>'ui form'])}}
            
            <input type="hidden" value="{{Auth::user()->id}}" name="user_id">
            
            <div class="field">
                <label for="name" class="text-left">Name (optional)</label>
                <input type="text" id="name" name="name" placeholder="My awesome asset">
            </div>
            <div class="field">
                <label for="tag" class="text-left">Tag (optional)</label>
                <input type="text" id="tag" name="tag" placeholder="some_asset_tag">
            </div>
            <div class="field">
                <label for="file" class="ui icon button">Browse</label>
                <input type="file" id="file" name="file" style="display:none">
            </div>
            
            <!-- shared -->
            <div class="field">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="shared"> Shared
                    </label>
                </div>
            </div>

            <div class="field">
                <button type="submit" class="ui button">Upload</button>
            </div>

            <div class="text-center">@include('site.partials.form-errors')</div>

        {{Form::close()}}

    </div>
</div>
